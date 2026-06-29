<?php

namespace App\Services\Agents;

use App\Models\Finance;
use App\Models\Reimbursement;
use App\Models\AnalyticsLog;
use Carbon\Carbon;

/**
 * FinancialMonitoringAgent
 * Monitors transactions, validates reimbursements, detects anomalies.
 */
class FinancialMonitoringAgent
{
    public function analyze(): array
    {
        $today     = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        $incomeToday      = Finance::where('type', 'income')->whereDate('created_at', $today)->sum('amount');
        $expenseToday     = Finance::where('type', 'expense')->whereDate('created_at', $today)->sum('amount');
        $incomeMonth      = Finance::where('type', 'income')->where('created_at', '>=', $thisMonth)->sum('amount');
        $expenseMonth     = Finance::where('type', 'expense')->where('created_at', '>=', $thisMonth)->sum('amount');
        $pendingReimburse = Reimbursement::where('status', 'pending')->count();
        $totalReimburse   = Reimbursement::where('status', 'pending')->sum('amount');

        $alerts         = [];
        $insights       = [];
        $recommendations= [];

        // Anomaly: expense > income today
        if ($expenseToday > $incomeToday && $incomeToday > 0) {
            $alerts[] = ['level' => 'warning', 'message' => '⚠️ Pengeluaran hari ini melebihi pemasukan. Periksa catatan pengeluaran.'];
        }
        if ($pendingReimburse > 0) {
            $alerts[] = ['level' => 'info', 'message' => "📋 {$pendingReimburse} permintaan reimburse menunggu persetujuan (Total: Rp " . number_format($totalReimburse, 0, ',', '.') . ")."];
        }
        if ($incomeMonth > 0 && $expenseMonth / $incomeMonth > 0.7) {
            $recommendations[] = '💡 Rasio biaya operasional melebihi 70% pendapatan bulan ini. Evaluasi pengeluaran.';
        }
        if ($incomeToday > 0 && $expenseToday === 0) {
            $insights[] = ['type' => 'success', 'message' => '✅ Tidak ada pengeluaran dicatat hari ini.'];
        }

        $result = [
            'income_today'       => $incomeToday,
            'expense_today'      => $expenseToday,
            'balance_today'      => $incomeToday - $expenseToday,
            'income_month'       => $incomeMonth,
            'expense_month'      => $expenseMonth,
            'balance_month'      => $incomeMonth - $expenseMonth,
            'pending_reimburse'  => $pendingReimburse,
            'total_reimburse'    => $totalReimburse,
            'alerts'             => $alerts,
            'insights'           => $insights,
            'recommendations'    => $recommendations,
        ];

        $this->log('finance_analysis', $result);
        return $result;
    }

    private function log(string $action, array $data): void
    {
        AnalyticsLog::create([
            'agent_type' => 'FinancialMonitoringAgent',
            'action'     => $action,
            'data'       => json_encode($data),
            'insight'    => "Pemasukan hari ini: Rp " . number_format($data['income_today'], 0, ',', '.'),
        ]);
    }
}
