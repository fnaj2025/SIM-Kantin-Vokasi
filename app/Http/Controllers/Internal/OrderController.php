<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\KitchenQueue;
use App\Events\OrderPlacedEvent;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('items')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(20)->withQueryString();

        return view('internal.orders', compact('orders'));
    }

    /**
     * Detail pesanan.
     * Jika request dari JS (Accept: application/json) → return JSON untuk slide panel.
     * Jika akses langsung browser → return view.
     */
    public function show(Request $request, Order $order)
    {
        $order->load('items');

        if ($request->expectsJson()) {
            return response()->json([
                'id'             => $order->id,
                'order_number'   => $order->order_number,
                'customer_name'  => $order->customer_name,
                'customer_phone' => $order->customer_phone,
                'status'         => $order->status,
                'payment_status' => $order->payment_status,
                'payment_method' => $order->payment_method,
                'source'         => $order->source,
                'order_type'     => $order->order_type,
                'subtotal'       => $order->subtotal,
                'total'          => $order->total,
                'notes'          => $order->notes,
                'created_at'     => $order->created_at->toIso8601String(),
                'items'          => $order->items->map(fn ($item) => [
                    'id'       => $item->id,
                    'name'     => $item->name,
                    'price'    => $item->price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal,
                    'notes'    => $item->notes,
                ]),
            ]);
        }

        return view('internal.order_detail', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status'         => 'required|in:menunggu,diproses,siap,selesai,dibatalkan',
            'payment_status' => 'required|in:belum_bayar,sudah_bayar',
        ]);

        $order->update($validated);

        if ($validated['status'] === 'diproses' && ! $order->kitchenQueue) {
            KitchenQueue::create([
                'order_id'             => $order->id,
                'status'               => 'pending',
                'estimated_completion' => now()->addMinutes(15),
            ]);
            broadcast(new OrderPlacedEvent($order))->toOthers();
        }

        if ($validated['status'] === 'selesai' && ! $order->completed_at) {
            $order->update(['completed_at' => now()]);
        }

        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui!');
    }

    /**
     * Hapus pesanan — hanya untuk status dibatalkan.
     */
    public function destroy(Order $order)
    {
        if ($order->status !== 'dibatalkan') {
            return redirect()->back()
                ->with('error', 'Hanya pesanan berstatus dibatalkan yang dapat dihapus.');
        }

        $orderNumber = $order->order_number;
        $order->delete();

        return redirect()->route('internal.orders.index')
            ->with('success', "Pesanan {$orderNumber} berhasil dihapus.");
    }
}