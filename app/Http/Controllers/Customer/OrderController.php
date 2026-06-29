<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\KitchenQueue;
use App\Events\OrderPlacedEvent;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function checkout()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('home');
        }
        return view('customer.checkout', compact('cart'));
    }

    public function store(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('home');
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:tunai,qris',
            'order_type'     => 'required|in:dine_in,takeaway',
        ]);

        $subtotal = 0;
        foreach ($cart as $item) {
            // Validate price from DB — same security fix as POS
            $menuItem = \App\Models\MenuItem::find($item['id']);
            if ($menuItem) {
                $subtotal += $menuItem->price * $item['quantity'];
            }
        }
        $user     = auth()->user();

        $order = Order::create([
            'order_number'   => 'ORD-' . strtoupper(uniqid()),
            'customer_name'  => $user->name,
            'customer_phone' => optional($user->customerProfile)->phone,
            'status'         => 'menunggu',
            'payment_method' => $validated['payment_method'],
            'payment_status' => 'belum_bayar',
            'source'         => 'online',
            'order_type'     => $validated['order_type'],
            'subtotal'       => $subtotal,
            'total'          => $subtotal,
            'processed_by'   => $user->id,  // ISU-05 FIX: store user_id for accurate history tracking
        ]);

        foreach ($cart as $item) {
            $menuItem = \App\Models\MenuItem::find($item['id']);
            if (!$menuItem) continue;
            OrderItem::create([
                'order_id'     => $order->id,
                'menu_item_id' => $menuItem->id,
                'name'         => $menuItem->name,
                'price'        => $menuItem->price,  // from DB
                'quantity'     => $item['quantity'],
                'subtotal'     => $menuItem->price * $item['quantity'],
                'notes'        => $item['notes'] ?? null,
            ]);
        }

        // Do NOT create KitchenQueue yet. 
        // Kasir must confirm and send to kitchen first.

        session()->forget('cart');

        return redirect()->route('orders.history')
            ->with('success', '🎉 Pesanan berhasil dibuat! Menunggu konfirmasi kasir.');
    }
}
