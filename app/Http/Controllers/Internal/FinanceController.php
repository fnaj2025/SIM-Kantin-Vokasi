<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\Finance;
use App\Models\Order;
use App\Models\Reimbursement;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $today     = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        // ── Filter tanggal untuk tabel transaksi ──────────────────────────
        $dateFrom = $request->filled('date_from')
            ? Carbon::parse($request->date_from)->startOfDay()
            : null;
        $dateTo   = $request->filled('date_to')
            ? Carbon::parse($request->date_to)->endOfDay()
            : null;

        $txQuery = Finance::with(['order', 'recordedBy'])->latest();
        if ($dateFrom) $txQuery->where('created_at', '>=', $dateFrom);
        if ($dateTo)   $txQuery->where('created_at', '<=', $dateTo);
        if ($request->filled('type')) $txQuery->where('type', $request->type);

        $transactions   = $txQuery->paginate(20)->withQueryString();
        $reimbursements = Reimbursement::with(['user', 'approvedBy'])->latest()->paginate(10);

        // ── Summary cards (selalu hari ini & bulan ini) ───────────────────
        $summary = [
            'income_today'      => Finance::where('type', 'income')->whereDate('created_at', $today)->sum('amount'),
            'expense_today'     => Finance::where('type', 'expense')->whereDate('created_at', $today)->sum('amount'),
            'income_month'      => Finance::where('type', 'income')->where('created_at', '>=', $thisMonth)->sum('amount'),
            'expense_month'     => Finance::where('type', 'expense')->where('created_at', '>=', $thisMonth)->sum('amount'),
            'pending_reimburse' => Reimbursement::where('status', 'pending')->count(),
        ];
        $summary['balance_today'] = $summary['income_today'] - $summary['expense_today'];
        $summary['balance_month'] = $summary['income_month'] - $summary['expense_month'];

        // ── Filter periode ringkasan ───────────────────────────────────────
        $summaryFrom = $request->filled('summary_from')
            ? Carbon::parse($request->summary_from)->startOfDay()
            : Carbon::now()->startOfWeek();
        $summaryTo   = $request->filled('summary_to')
            ? Carbon::parse($request->summary_to)->endOfDay()
            : Carbon::now()->endOfDay();

        $periodIncome  = Finance::where('type', 'income')
            ->whereBetween('created_at', [$summaryFrom, $summaryTo])->sum('amount');
        $periodExpense = Finance::where('type', 'expense')
            ->whereBetween('created_at', [$summaryFrom, $summaryTo])->sum('amount');

        // Grafik arus kas harian dalam periode
        $cashflowChart = [];
        $diffDays = min((int) $summaryFrom->diffInDays($summaryTo) + 1, 31);
        for ($i = 0; $i < $diffDays; $i++) {
            $date = $summaryFrom->copy()->addDays($i);
            $cashflowChart[] = [
                'date'    => $date->format('d/m'),
                'income'  => Finance::where('type', 'income')->whereDate('created_at', $date)->sum('amount'),
                'expense' => Finance::where('type', 'expense')->whereDate('created_at', $date)->sum('amount'),
            ];
        }

        // Pengeluaran per kategori dalam periode
        $expenseByCategory = Finance::where('type', 'expense')
            ->whereBetween('created_at', [$summaryFrom, $summaryTo])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        // Metode pembayaran (order sudah bayar dalam periode)
        $qrisTotal  = Order::where('payment_method', 'qris')
            ->where('payment_status', 'sudah_bayar')
            ->whereBetween('created_at', [$summaryFrom, $summaryTo])
            ->sum('total');
        $tunaiTotal = Order::where('payment_method', 'tunai')
            ->where('payment_status', 'sudah_bayar')
            ->whereBetween('created_at', [$summaryFrom, $summaryTo])
            ->sum('total');
        $paymentTotal = $qrisTotal + $tunaiTotal;

        return view('internal.finance', compact(
            'transactions', 'reimbursements', 'summary',
            'summaryFrom', 'summaryTo',
            'periodIncome', 'periodExpense',
            'cashflowChart', 'expenseByCategory',
            'qrisTotal', 'tunaiTotal', 'paymentTotal'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'        => 'required|in:income,expense',
            'amount'      => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
            'category'    => 'nullable|string|max:100',
        ]);

        $validated['recorded_by'] = auth()->id();
        Finance::create($validated);

        try { broadcast(new \App\Events\FinanceUpdatedEvent())->toOthers(); } catch (\Exception $e) {}

        return redirect()->route('internal.finance.index')->with('success', 'Transaksi berhasil dicatat!');
    }

    /**
     * Verifikasi pembayaran order online → otomatis catat income ke Finance.
     * Dipanggil dari halaman orders internal (tombol Bayar).
     */
    public function verifyPayment(Request $request, Order $order)
    {
        // Cegah double entry finance
        $alreadyRecorded = Finance::where('order_id', $order->id)->where('type', 'income')->exists();

        if (! $alreadyRecorded) {
            Finance::create([
                'type'        => 'income',
                'amount'      => $order->total,
                'description' => 'Pembayaran ' . strtoupper($order->source) . ': ' . $order->order_number . ' (' . $order->customer_name . ')',
                'category'    => $order->source === 'online' ? 'Penjualan Online' : 'Penjualan',
                'order_id'    => $order->id,
                'recorded_by' => auth()->id(),
            ]);

            try { broadcast(new \App\Events\FinanceUpdatedEvent())->toOthers(); } catch (\Exception $e) {}
        }

        $order->update(['payment_status' => 'sudah_bayar']);

        return redirect()->back()->with('success',
            'Pembayaran ' . $order->order_number . ' dikonfirmasi dan masuk ke laporan keuangan!');
    }

    public function approveReimbursement(Request $request, Reimbursement $reimbursement)
    {
        $validated = $request->validate([
            'action' => 'required|in:approved,rejected',
            'notes'  => 'nullable|string',
        ]);

        $reimbursement->update([
            'status'      => $validated['action'],
            'notes'       => $validated['notes'] ?? null,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        if ($validated['action'] === 'approved') {
            Finance::create([
                'type'        => 'expense',
                'amount'      => $reimbursement->amount,
                'description' => 'Reimburse: ' . $reimbursement->description,
                'category'    => 'Reimbursement',
                'recorded_by' => auth()->id(),
            ]);
            try { broadcast(new \App\Events\FinanceUpdatedEvent())->toOthers(); } catch (\Exception $e) {}
        }

        $msg = $validated['action'] === 'approved' ? 'Reimburse disetujui!' : 'Reimburse ditolak.';
        return redirect()->route('internal.finance.index')->with('success', $msg);
    }

    public function storeReimbursement(Request $request)
    {
        $validated = $request->validate([
            'amount'      => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
            'category'    => 'nullable|string|max:100',
        ]);

        $path = null;
        if ($request->hasFile('receipt')) {
            $path = $request->file('receipt')->store('receipts', 'public');
        }

        Reimbursement::create([
            'user_id'      => auth()->id(),
            'status'       => 'pending',
            'amount'       => $validated['amount'],
            'description'  => $validated['description'],
            'category'     => $validated['category'] ?? null,
            'receipt_path' => $path,
        ]);

        return redirect()->route('internal.finance.index')->with('success', 'Permintaan reimburse berhasil dikirim!');
    }
}