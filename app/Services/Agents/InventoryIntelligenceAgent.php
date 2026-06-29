<?php

namespace App\Services\Agents;

use App\Models\Order;
use App\Models\KitchenQueue;
use App\Models\InventoryItem;
use App\Models\Finance;
use App\Models\AnalyticsLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * InventoryIntelligenceAgent
 * Analyzes stock levels, predicts shortages, and generates restock recommendations.
 */
class InventoryIntelligenceAgent
{
    public function analyze(): array
    {
        $items = InventoryItem::all();
        $insights = [];
        $alerts = [];
        $recommendations = [];

        foreach ($items as $item) {
            $ratio = $item->minimum_stock > 0 ? $item->stock / $item->minimum_stock : 99;

            if ($item->stock <= 0) {
                $alerts[] = ['level' => 'critical', 'message' => "🚨 {$item->name} HABIS TOTAL! Segera restock."];
            } elseif ($ratio <= 1) {
                $alerts[] = ['level' => 'warning', 'message' => "⚠️ {$item->name} menipis ({$item->stock} {$item->unit} tersisa). Batas minimum: {$item->minimum_stock}."];
                $recommendations[] = "📦 Buat permintaan pengadaan untuk {$item->name} minimal " . ($item->minimum_stock * 3) . " {$item->unit}.";
            }
        }

        $lowStockCount  = InventoryItem::whereRaw('stock <= minimum_stock')->count();
        $criticalCount  = InventoryItem::where('stock', '<=', 0)->count();
        $healthyCount   = $items->count() - $lowStockCount - $criticalCount;

        if (empty($alerts)) {
            $insights[] = ['type' => 'success', 'message' => '✅ Seluruh stok inventori dalam kondisi aman.'];
        }

        $result = [
            'low_stock_count'   => $lowStockCount,
            'critical_count'    => $criticalCount,
            'healthy_count'     => $healthyCount,
            'alerts'            => $alerts,
            'recommendations'   => $recommendations,
            'insights'          => $insights,
        ];

        $this->log('inventory_analysis', $result);
        return $result;
    }

    private function log(string $action, array $data): void
    {
        AnalyticsLog::create([
            'agent_type' => 'InventoryIntelligenceAgent',
            'action'     => $action,
            'data'       => json_encode($data),
            'insight'    => "Stok kritis: {$data['critical_count']}, Menipis: {$data['low_stock_count']}",
        ]);
    }
}
