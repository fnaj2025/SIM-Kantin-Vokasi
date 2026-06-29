<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['items.menuItem', 'kitchenQueue'])
                      ->orderBy('created_at', 'desc');

        // If user is logged in, show their orders by default
        if (Auth::check()) {
            $query->where('customer_name', Auth::user()->name);
        }

        // Search by order number if provided
        if ($request->filled('search')) {
            $query->where('order_number', 'LIKE', '%' . $request->search . '%');
        }

        $orders = $query->get();

        return view('customer.order-history', compact('orders'));
    }
}
