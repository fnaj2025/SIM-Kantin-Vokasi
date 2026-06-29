<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Finance;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        // Simple aggregate data for reports
        $totalOrders = Order::count();
        $completedOrders = Order::where('status', 'selesai')->count();
        $totalRevenue = Finance::where('type', 'income')->sum('amount');

        return view('internal.reports', compact('totalOrders', 'completedOrders', 'totalRevenue'));
    }
}
