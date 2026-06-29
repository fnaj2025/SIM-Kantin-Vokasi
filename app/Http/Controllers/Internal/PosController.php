<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\KitchenQueue;
use App\Models\Finance;
use App\Events\OrderPlacedEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $menuItems  = MenuItem::where('is_available', true)->with('category')->get();

        return view('internal.pos', compact('categories', 'menuItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'    => 'required|string|max:255',
            'payment_method'   => 'required|in:tunai,qris',
            'order_type'       => 'required|in:dine_in,takeaway',
            'items'            => 'required|array',
            'items.*.id'       => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price'    => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            // BUG-01 FIX: Always use DB price — never trust frontend price
            $subtotal = 0;
            $orderItemsData = [];
            foreach ($validated['items'] as $item) {
                $menuItem = MenuItem::findOrFail($item['id']);
                $lineTotal = $menuItem->price * $item['quantity'];
                $subtotal += $lineTotal;
                $orderItemsData[] = [
                    'menu_item_id' => $menuItem->id,
                    'name'         => $menuItem->name,
                    'price'        => $menuItem->price,  // from DB
                    'quantity'     => $item['quantity'],
                    'subtotal'     => $lineTotal,
                ];
            }

            $order = Order::create([
                'order_number'   => 'ORD-' . strtoupper(uniqid()),
                'customer_name'  => $validated['customer_name'],
                'status'         => 'menunggu',
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'sudah_bayar',
                'source'         => 'pos',
                'order_type'     => $validated['order_type'],
                'subtotal'       => $subtotal,
                'total'          => $subtotal,
                'processed_by'   => auth()->id(),
            ]);

            foreach ($orderItemsData as $itemData) {
                OrderItem::create(array_merge(['order_id' => $order->id], $itemData));
            }

            KitchenQueue::create([
                'order_id'             => $order->id,
                'status'               => 'pending',
                'estimated_completion' => now()->addMinutes(15),
            ]);

            Finance::create([
                'type'        => 'income',
                'amount'      => $subtotal,
                'description' => 'Penjualan POS ' . $order->order_number,
                'category'    => 'Penjualan',
                'order_id'    => $order->id,
                'recorded_by' => auth()->id(),
            ]);

            DB::commit();

            broadcast(new OrderPlacedEvent($order))->toOthers();

            return response()->json([
                'success'      => true,
                'order_number' => $order->order_number,
                'total'        => $subtotal,
                'message'      => 'Pesanan berhasil masuk ke antrian dapur!',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
