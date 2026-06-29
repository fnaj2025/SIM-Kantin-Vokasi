<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('customer.cart', compact('cart'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id',
            'quantity'     => 'nullable|integer|min:1|max:50',
        ]);

        $item     = MenuItem::findOrFail($request->menu_item_id);
        $qty      = max(1, (int) ($request->quantity ?? 1));
        $cart     = session()->get('cart', []);

        if (isset($cart[$item->id])) {
            // Sudah ada → tambahkan qty yang dipilih
            $cart[$item->id]['quantity'] += $qty;
            $cart[$item->id]['subtotal']  = $cart[$item->id]['quantity'] * $item->price;
        } else {
            // Belum ada → buat entry baru, sertakan image dari DB
            $cart[$item->id] = [
                'id'       => $item->id,
                'name'     => $item->name,
                'price'    => $item->price,
                'image'    => $item->image,   // ← FIX: simpan URL gambar dari DB
                'quantity' => $qty,
                'subtotal' => $item->price * $qty,
                'notes'    => $request->notes ?? '',
            ];
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', $item->name . ' berhasil ditambahkan ke keranjang!');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'       => 'required',
            'quantity' => 'required|integer|min:1|max:50',
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$request->id])) {
            $cart[$request->id]['quantity'] = (int) $request->quantity;
            $cart[$request->id]['subtotal'] = (int) $request->quantity * $cart[$request->id]['price'];
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index');
    }

    public function remove(Request $request)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$request->id])) {
            unset($cart[$request->id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index');
    }
}