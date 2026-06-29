<?php

namespace App\Services\Agents;

use App\Models\Order;
use App\Models\KitchenQueue;
use App\Models\AnalyticsLog;
use Carbon\Carbon;

/**
 * KitchenWorkflowAgent
 * Monitors kitchen queue, predicts delays, and suggests workload balancing.
 */
class KitchenWorkflowAgent
{
    public function analyze(): array
    {
        $activeQueues = KitchenQueue::with('order')
            ->whereNotIn('status', ['delivered'])
            ->get();

        $overdue     = $activeQueues->filter(fn($q) => $q->estimated_completion && now()->gt($q->estimated_completion));
        $pending     = $activeQueues->where('status', 'pending')->count();
        $inProgress  = $activeQueues->whereIn('status', ['preparing', 'cooking'])->count();
        $ready       = $activeQueues->where('status', 'ready')->count();

        $insights       = [];
        $recommendations= [];
        $alerts         = [];

        if ($overdue->count() > 0) {
            $alerts[] = ['level' => 'danger', 'message' => "🔴 {$overdue->count()} pesanan melebihi estimasi waktu! Percepat proses dapur."];
        }
        if ($pending > 5) {
            $alerts[] = ['level' => 'warning', 'message' => "⚠️ Antrian dapur menumpuk ({$pending} pesanan menunggu). Prioritaskan pesanan lama."];
            $recommendations[] = "👨‍🍳 Pertimbangkan menambah staf dapur atau menyederhanakan menu saat jam sibuk.";
        }
        if ($pending === 0 && $inProgress === 0 && $ready === 0) {
            $insights[] = ['type' => 'success', 'message' => '✅ Dapur sedang dalam kondisi idle. Semua pesanan telah diproses.'];
        }

        $avgCompletionMinutes = $this->avgCompletionTime();

        $result = [
            'active_count'           => $activeQueues->count(),
            'pending'                => $pending,
            'in_progress'            => $inProgress,
            'ready'                  => $ready,
            'overdue_count'          => $overdue->count(),
            'avg_completion_minutes' => $avgCompletionMinutes,
            'alerts'                 => $alerts,
            'recommendations'        => $recommendations,
            'insights'               => $insights,
        ];

        $this->log('kitchen_analysis', $result);
        return $result;
    }

    private function avgCompletionTime(): float
    {
        $completed = KitchenQueue::where('status', 'delivered')
            ->whereNotNull('started_at')
            ->whereNotNull('completed_at')
            ->get();

        if ($completed->isEmpty()) return 0;

        $totalMinutes = $completed->sum(fn($q) => $q->started_at->diffInMinutes($q->completed_at));
        return round($totalMinutes / $completed->count(), 1);
    }

    private function log(string $action, array $data): void
    {
        AnalyticsLog::create([
            'agent_type' => 'KitchenWorkflowAgent',
            'action'     => $action,
            'data'       => json_encode($data),
            'insight'    => "Antrian: {$data['active_count']}, Terlambat: {$data['overdue_count']}",
        ]);
    }
}
