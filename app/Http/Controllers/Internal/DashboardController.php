<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Finance;
use App\Models\InventoryItem;
use App\Models\KitchenQueue;
use App\Services\Agents\InventoryIntelligenceAgent;
use App\Services\Agents\KitchenWorkflowAgent;
use App\Services\Agents\FinancialMonitoringAgent;
use App\Services\Agents\OperationalAnalyticsAgent;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Real metrics
        $stats = [
            'orders_today'    => Order::whereDate('created_at', $today)->count(),
            'pending_orders'  => KitchenQueue::whereIn('status', ['pending', 'preparing', 'cooking'])->count(),
            'income_today'    => Finance::where('type', 'income')->whereDate('created_at', $today)->sum('amount'),
            'low_stock_items' => InventoryItem::whereRaw('stock <= minimum_stock')->count(),
            'active_kitchen'  => KitchenQueue::whereNotIn('status', ['delivered'])->count(),
            'completed_today' => Order::whereDate('created_at', $today)->where('status', 'selesai')->count(),
        ];

        $recentOrders = Order::with('items')->latest()->take(8)->get();

        // Run AI Agents
        try {
            $inventoryAgent  = (new InventoryIntelligenceAgent())->analyze();
            $kitchenAgent    = (new KitchenWorkflowAgent())->analyze();
            $financeAgent    = (new FinancialMonitoringAgent())->analyze();
            $analyticsAgent  = (new OperationalAnalyticsAgent())->analyze();
        } catch (\Exception $e) {
            $inventoryAgent = $kitchenAgent = $financeAgent = $analyticsAgent = ['alerts' => [], 'insights' => [], 'recommendations' => []];
        }

        // Collect all AI alerts
        $aiAlerts = array_merge(
            $inventoryAgent['alerts']  ?? [],
            $kitchenAgent['alerts']    ?? [],
            $financeAgent['alerts']    ?? [],
            $analyticsAgent['alerts']  ?? []
        );
        $aiInsights = array_merge(
            $inventoryAgent['insights']  ?? [],
            $kitchenAgent['insights']    ?? [],
            $financeAgent['insights']    ?? [],
            $analyticsAgent['insights']  ?? []
        );

        // Revenue chart (last 7 days)
        $revenueChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $revenueChart[] = [
                'date'    => $date->format('D'),
                'revenue' => Finance::where('type', 'income')->whereDate('created_at', $date)->sum('amount'),
                'orders'  => Order::whereDate('created_at', $date)->count(),
            ];
        }

        return view('internal.dashboard', compact(
            'stats', 'recentOrders',
            'inventoryAgent', 'kitchenAgent', 'financeAgent', 'analyticsAgent',
            'aiAlerts', 'aiInsights', 'revenueChart'
        ));
    }
}
