<?php

namespace App\Services\Agents;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\AnalyticsLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * OperationalAnalyticsAgent
 * Analyzes sales performance, peak hours, top menus, and operational efficiency.
 */
class OperationalAnalyticsAgent
{
    public function analyze(): array
    {
        $today     = Carbon::today();
        $thisWeek  = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        // Orders metrics
        $ordersToday   = Order::whereDate('created_at', $today)->count();
        $ordersWeek    = Order::where('created_at', '>=', $thisWeek)->count();
        $ordersMonth   = Order::where('created_at', '>=', $thisMonth)->count();
        $completedRate = $ordersToday > 0
            ? round(Order::whereDate('created_at', $today)->where('status', 'selesai')->count() / $ordersToday * 100, 1)
            : 0;

        // Fetch today's orders once
        $todayOrders = Order::whereDate('created_at', $today)->get();

        // Peak hours: group by hour
        $peakHours = $todayOrders
            ->groupBy(fn($order) => $order->created_at->format('H'))
            ->map(fn($group) => $group->count())
            ->sortDesc()
            ->take(3)
            ->toArray();

        // Top selling menu items (this week)
        $topMenus = OrderItem::with('menuItem')
            ->where('created_at', '>=', $thisWeek)
            ->select('menu_item_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
            ->groupBy('menu_item_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get()
            ->map(fn($i) => [
                'name'    => optional($i->menuItem)->name ?? 'Unknown',
                'qty'     => $i->total_qty,
                'revenue' => $i->total_revenue,
            ])
            ->toArray();

        // Hourly order data for chart (today)
        $hourlyData = [];
        for ($h = 7; $h <= 20; $h++) {
            $formattedH = str_pad($h, 2, '0', STR_PAD_LEFT);
            $count = $todayOrders->filter(fn($order) => $order->created_at->format('H') === $formattedH)->count();
            $hourlyData[] = ['hour' => "{$formattedH}:00", 'count' => $count];
        }

        // Recommendations
        $recommendations = [];
        $alerts          = [];
        $insights        = [];

        if ($completedRate < 70 && $ordersToday > 0) {
            $alerts[] = ['level' => 'warning', 'message' => "⚠️ Tingkat penyelesaian pesanan hari ini hanya {$completedRate}%. Percepat proses dapur."];
        }
        if ($ordersToday > 20) {
            $insights[] = ['type' => 'success', 'message' => "🎉 Hari ini sudah ada {$ordersToday} pesanan — hari yang sibuk!"];
            $recommendations[] = '🥗 Pertimbangkan menambah varian menu untuk mengakomodasi lonjakan permintaan.';
        }
        if (!empty($topMenus)) {
            $top = $topMenus[0];
            $insights[] = ['type' => 'info', 'message' => "🏆 Menu terlaris minggu ini: {$top['name']} ({$top['qty']} porsi terjual)."];
        }

        $result = [
            'orders_today'    => $ordersToday,
            'orders_week'     => $ordersWeek,
            'orders_month'    => $ordersMonth,
            'completed_rate'  => $completedRate,
            'peak_hours'      => $peakHours,
            'top_menus'       => $topMenus,
            'hourly_data'     => $hourlyData,
            'alerts'          => $alerts,
            'insights'        => $insights,
            'recommendations' => $recommendations,
        ];

        $this->log('operational_analysis', $result);
        return $result;
    }

    private function log(string $action, array $data): void
    {
        AnalyticsLog::create([
            'agent_type' => 'OperationalAnalyticsAgent',
            'action'     => $action,
            'data'       => json_encode(['summary' => ['orders_today' => $data['orders_today']]]),
            'insight'    => "Pesanan hari ini: {$data['orders_today']}, Selesai: {$data['completed_rate']}%",
        ]);
    }
}
