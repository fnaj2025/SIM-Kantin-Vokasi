
<?php $__env->startSection('title', 'Keuangan & Reimburse'); ?>

<?php $__env->startSection('content'); ?>

<?php
    $summaryFrom       = $summaryFrom       ?? \Carbon\Carbon::now()->startOfWeek();
    $summaryTo         = $summaryTo         ?? \Carbon\Carbon::now()->endOfDay();
    $periodIncome      = $periodIncome      ?? 0;
    $periodExpense     = $periodExpense     ?? 0;
    $cashflowChart     = $cashflowChart     ?? [];
    $expenseByCategory = $expenseByCategory ?? collect();
    $qrisTotal         = $qrisTotal         ?? 0;
    $tunaiTotal        = $tunaiTotal        ?? 0;
    $paymentTotal      = $paymentTotal      ?? 0;
?>
<style>
.fin-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem;margin-bottom:2rem;}
.fin-stat{background:#FFF;border-radius:1rem;padding:1.5rem;border:1px solid #E2E8F0;box-shadow:0 4px 6px -1px rgba(0,0,0,.02);position:relative;overflow:hidden;transition:.3s;}
.fin-stat:hover{transform:translateY(-4px);box-shadow:0 12px 20px -5px rgba(0,0,0,.05);}
.fin-stat-header{display:flex;align-items:center;gap:.5rem;margin-bottom:.75rem;}
.fin-stat-icon{display:flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:.375rem;}
.fin-stat-label{font-size:.75rem;font-weight:700;color:#64748B;text-transform:uppercase;letter-spacing:.05em;}
.fin-stat-value{font-size:1.75rem;font-weight:800;margin-bottom:.25rem;}
.fin-stat-sub{font-size:.85rem;color:#64748B;}
.fin-stat-bg{position:absolute;right:0;bottom:0;width:120px;height:60px;opacity:.5;pointer-events:none;}
.tabs{display:flex;gap:1rem;border-bottom:1px solid #E2E8F0;padding-bottom:0;}
.tab-btn{padding:.75rem 1rem;font-size:.9rem;font-weight:600;background:none;border:none;border-bottom:2px solid transparent;cursor:pointer;color:#64748B;transition:all .2s;display:flex;align-items:center;gap:.5rem;margin-bottom:-1px;}
.tab-btn:hover{color:#0F172A;}
.tab-btn.active{color:#EA580C;border-bottom-color:#EA580C;}
.btn-add{background:#EA580C;color:white;border:none;padding:.5rem 1.25rem;border-radius:.5rem;font-weight:700;font-size:.85rem;cursor:pointer;transition:.2s;display:inline-flex;align-items:center;gap:.5rem;}
.btn-add:hover{background:#C2410C;transform:translateY(-1px);box-shadow:0 4px 12px rgba(234,88,12,.2);}
.btn-outline-sm{background:white;color:#64748B;border:1.5px solid #E2E8F0;padding:.4rem .875rem;border-radius:.5rem;font-weight:600;font-size:.8rem;cursor:pointer;transition:.2s;display:inline-flex;align-items:center;gap:.4rem;}
.btn-outline-sm:hover{border-color:#94A3B8;color:#0F172A;}
.filter-bar{display:flex;gap:.75rem;align-items:center;flex-wrap:wrap;padding:1rem 1.25rem;background:#F8FAFC;border-bottom:1px solid #E2E8F0;}
.filter-input{padding:.4rem .75rem;border:1.5px solid #E2E8F0;border-radius:.5rem;font-size:.8rem;font-family:inherit;color:#0F172A;background:white;transition:.2s;}
.filter-input:focus{outline:none;border-color:#EA580C;box-shadow:0 0 0 3px rgba(234,88,12,.1);}
.table-card{background:#FFF;border-radius:1rem;border:1px solid #E2E8F0;overflow:hidden;margin-bottom:2rem;box-shadow:0 4px 6px -1px rgba(0,0,0,.02);}
.data-table{width:100%;border-collapse:collapse;}
.data-table th{font-size:.75rem;font-weight:700;color:#64748B;text-transform:uppercase;padding:1rem 1.25rem;border-bottom:1px solid #E2E8F0;background:#F8FAFC;text-align:left;white-space:nowrap;}
.data-table td{padding:.875rem 1.25rem;font-size:.875rem;border-bottom:1px dashed #E2E8F0;color:#0F172A;}
.data-table tr:last-child td{border-bottom:none;}
.data-table tr:hover td{background:#FAFAFA;}
.pill{display:inline-flex;align-items:center;gap:.25rem;padding:.25rem .75rem;border-radius:9999px;font-size:.75rem;font-weight:700;}
.pill-income{background:#ECFDF5;color:#059669;}
.pill-expense{background:#FEF2F2;color:#DC2626;}
.category-badge{background:#F1F5F9;color:#3B82F6;padding:.25rem .75rem;border-radius:9999px;font-size:.75rem;font-weight:600;display:inline-block;}
.summary-grid{display:grid;grid-template-columns:2fr 1fr 1fr;gap:1.5rem;margin-bottom:2rem;}
.summary-card{background:#FFF;border-radius:1rem;padding:1.5rem;border:1px solid #E2E8F0;box-shadow:0 4px 6px -1px rgba(0,0,0,.02);display:flex;flex-direction:column;}
.summary-card-title{font-size:.9rem;font-weight:700;color:#0F172A;margin-bottom:1.25rem;display:flex;align-items:center;gap:.5rem;}
.chart-bars{display:flex;align-items:flex-end;gap:4px;height:160px;padding-bottom:1.5rem;position:relative;border-bottom:1px solid #E2E8F0;}
.chart-bar-group{flex:1;display:flex;gap:2px;align-items:flex-end;position:relative;}
.chart-bar{flex:1;border-radius:3px 3px 0 0;min-height:2px;transition:opacity .2s;cursor:pointer;}
.chart-bar:hover{opacity:.8;}
.chart-label{position:absolute;bottom:-1.25rem;left:50%;transform:translateX(-50%);font-size:.6rem;color:#94A3B8;font-weight:700;white-space:nowrap;}
.cat-row{display:flex;align-items:center;gap:.75rem;padding:.625rem 0;border-bottom:1px dashed #F1F5F9;}
.cat-row:last-child{border-bottom:none;}
.cat-bar-bg{flex:1;height:6px;background:#F1F5F9;border-radius:3px;overflow:hidden;}
.cat-bar-fill{height:100%;border-radius:3px;background:#3B82F6;}
.pay-method{background:#F8FAFC;border:1px solid #E2E8F0;border-radius:.75rem;padding:1rem;margin-bottom:.75rem;}
.pay-header{display:flex;align-items:center;gap:.75rem;margin-bottom:.75rem;}
.pay-icon{width:32px;height:32px;background:white;border-radius:.5rem;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 4px rgba(0,0,0,.05);}
.progress-bar{height:6px;background:#E2E8F0;border-radius:3px;overflow:hidden;margin-top:.5rem;}
.progress-fill{height:100%;border-radius:3px;}
.modal-overlay{position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:200;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(4px);}
.modal-card{background:#FFF;border-radius:1.25rem;padding:2rem;width:100%;max-width:500px;box-shadow:0 25px 50px -12px rgba(0,0,0,.15);}
.form-group{margin-bottom:1rem;}
.form-label{display:block;font-size:.75rem;font-weight:700;color:#64748B;text-transform:uppercase;margin-bottom:.5rem;}
.form-input{width:100%;padding:.75rem 1rem;border:1.5px solid #E2E8F0;border-radius:.5rem;font-size:.9rem;font-family:inherit;background:#F8FAFC;transition:.2s;}
.form-input:focus{outline:none;border-color:#EA580C;background:white;box-shadow:0 0 0 3px rgba(234,88,12,.1);}
@media print{
  .no-print,nav,aside,header,.topbar,.sidebar,form,button:not(.print-keep){display:none!important;}
  body,.main-wrapper,.page-content{background:white!important;padding:0!important;margin:0!important;}
  .fin-stats{grid-template-columns:repeat(3,1fr);}
  .summary-grid{grid-template-columns:1fr 1fr 1fr;}
  .fin-stat,.summary-card,.table-card{box-shadow:none;border:1px solid #E2E8F0;break-inside:avoid;}
  .data-table{font-size:11px;}
  .print-header{display:block!important;text-align:center;margin-bottom:2rem;padding-bottom:1rem;border-bottom:2px solid #E2E8F0;}
}
@media(max-width:900px){.fin-stats,.summary-grid{grid-template-columns:1fr;}}
</style>


<div class="print-header" style="display:none;">
    <h1 style="font-size:1.5rem;font-weight:900;">KantinVokasi — Laporan Keuangan</h1>
    <?php if(isset($summaryFrom) && isset($summaryTo)): ?>
    <p style="color:#64748B;font-size:.9rem;">Periode: <?php echo e($summaryFrom->format('d M Y')); ?> – <?php echo e($summaryTo->format('d M Y')); ?></p>
    <?php endif; ?>
    <p style="color:#64748B;font-size:.85rem;">Dicetak: <?php echo e(now()->isoFormat('dddd, D MMMM YYYY · HH:mm')); ?></p>
</div>


<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.75rem;flex-wrap:wrap;gap:1rem;" class="no-print">
    <div>
        <h1 class="page-title">Keuangan & Reimburse</h1>
        <p class="page-sub">Kelola transaksi, reimburse, dan pantau arus kas</p>
    </div>
    <button onclick="window.print()" class="btn-outline-sm">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        Cetak Laporan
    </button>
</div>


<div class="fin-stats">
    <div class="fin-stat">
        <div class="fin-stat-header">
            <div class="fin-stat-icon" style="background:#FFEDD5;color:#EA580C;"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
            <div class="fin-stat-label">Pemasukan Hari Ini</div>
        </div>
        <div class="fin-stat-value" style="color:#10B981">Rp <?php echo e(number_format($summary['income_today'],0,',','.')); ?></div>
        <div class="fin-stat-sub">Bulan: Rp <?php echo e(number_format($summary['income_month'],0,',','.')); ?></div>
        <div class="fin-stat-bg"><svg viewBox="0 0 100 50" preserveAspectRatio="none" style="width:100%;height:100%;stroke:#10B981;fill:none;stroke-width:2;stroke-linecap:round;"><path d="M0 40 L20 30 L40 35 L60 15 L80 20 L100 5"/></svg></div>
    </div>
    <div class="fin-stat">
        <div class="fin-stat-header">
            <div class="fin-stat-icon" style="background:#FEE2E2;color:#DC2626;"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21 16-4 4-4-4"/><path d="M17 20V4"/><path d="m3 8 4-4 4 4"/><path d="M7 4v16"/></svg></div>
            <div class="fin-stat-label">Pengeluaran Hari Ini</div>
        </div>
        <div class="fin-stat-value" style="color:#DC2626">Rp <?php echo e(number_format($summary['expense_today'],0,',','.')); ?></div>
        <div class="fin-stat-sub">Bulan: Rp <?php echo e(number_format($summary['expense_month'],0,',','.')); ?></div>
        <div class="fin-stat-bg"><svg viewBox="0 0 100 50" preserveAspectRatio="none" style="width:100%;height:100%;stroke:#DC2626;fill:none;stroke-width:2;stroke-linecap:round;"><path d="M0 40 L20 45 L40 25 L60 30 L80 10 L100 15"/></svg></div>
    </div>
    <div class="fin-stat">
        <div class="fin-stat-header">
            <div class="fin-stat-icon" style="background:#FEF3C7;color:#D97706;"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><path d="M18 12a2 2 0 0 0 0 4h4v-4Z"/></svg></div>
            <div class="fin-stat-label">Saldo Bersih Bulan Ini</div>
        </div>
        <div class="fin-stat-value" style="color:<?php echo e($summary['balance_month']>=0?'#10B981':'#EF4444'); ?>">
            Rp <?php echo e(number_format(abs($summary['balance_month']),0,',','.')); ?><?php echo e($summary['balance_month']<0?' (defisit)':''); ?>

        </div>
        <?php if($summary['pending_reimburse']>0): ?>
            <div style="font-size:.75rem;color:#D97706;margin-top:.25rem;font-weight:600;display:flex;align-items:center;gap:.25rem;"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg><?php echo e($summary['pending_reimburse']); ?> reimburse pending</div>
        <?php else: ?>
            <div class="fin-stat-sub">Saldo aman</div>
        <?php endif; ?>
        <div class="fin-stat-bg"><svg viewBox="0 0 100 50" preserveAspectRatio="none" style="width:100%;height:100%;stroke:#D97706;fill:none;stroke-width:2;stroke-linecap:round;"><path d="M0 30 L20 15 L40 35 L60 20 L80 25 L100 10"/></svg></div>
    </div>
</div>


<div x-data="{tab:'transactions'}" class="no-print">
    <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:1rem;flex-wrap:wrap;gap:1rem;">
        <div class="tabs" style="border-bottom:none;margin-bottom:0;">
            <button class="tab-btn" :class="{active:tab==='transactions'}" @click="tab='transactions'">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Transaksi
            </button>
            <button class="tab-btn" :class="{active:tab==='reimburse'}" @click="tab='reimburse'">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><path d="M12 17.5v-11"/></svg>
                Reimbursement
            </button>
        </div>
        <button onclick="openFinanceModal()" class="btn-add" id="btnTambah">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah
        </button>
    </div>

    
    <div x-show="tab==='transactions'">
        <div class="table-card">
            <form method="GET" action="<?php echo e(route('internal.finance.index')); ?>">
                <div class="filter-bar">
                    <span style="font-size:.8rem;font-weight:700;color:#64748B;">Filter:</span>
                    <input type="date" name="date_from" class="filter-input" value="<?php echo e(request('date_from')); ?>">
                    <span style="color:#94A3B8;">—</span>
                    <input type="date" name="date_to" class="filter-input" value="<?php echo e(request('date_to')); ?>">
                    <select name="type" class="filter-input" style="min-width:130px;">
                        <option value="">Semua Tipe</option>
                        <option value="income"  <?php echo e(request('type')==='income' ?'selected':''); ?>>Pemasukan</option>
                        <option value="expense" <?php echo e(request('type')==='expense'?'selected':''); ?>>Pengeluaran</option>
                    </select>
                    <button type="submit" class="btn-add" style="padding:.4rem .875rem;font-size:.8rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        Terapkan
                    </button>
                    <?php if(request()->hasAny(['date_from','date_to','type'])): ?>
                    <a href="<?php echo e(route('internal.finance.index')); ?>" style="font-size:.8rem;color:#94A3B8;font-weight:600;text-decoration:none;display:flex;align-items:center;gap:.25rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>Reset
                    </a>
                    <?php endif; ?>
                    <span style="margin-left:auto;font-size:.8rem;color:#94A3B8;font-weight:600;">
                        <?php echo e($transactions->total()); ?> transaksi
                        <?php if(request()->hasAny(['date_from','date_to','type'])): ?>
                            <span style="color:#EA580C;">(difilter)</span>
                        <?php endif; ?>
                    </span>
                </div>
            </form>
            <div style="overflow-x:auto;">
                <table class="data-table">
                    <thead><tr><th>Tanggal</th><th>Tipe</th><th>Deskripsi</th><th>Kategori</th><th>Jumlah</th><th>Dicatat Oleh</th></tr></thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td style="color:#64748B;white-space:nowrap;"><?php echo e($tx->created_at->format('d M Y H:i')); ?></td>
                            <td><span class="pill <?php echo e($tx->type==='income'?'pill-income':'pill-expense'); ?>">
                                <?php if($tx->type==='income'): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m18 15-6-6-6 6"/></svg> Pemasukan
                                <?php else: ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg> Pengeluaran
                                <?php endif; ?>
                            </span></td>
                            <td style="font-weight:500;max-width:280px;"><?php echo e($tx->description); ?></td>
                            <td><span class="category-badge"><?php echo e($tx->category??'-'); ?></span></td>
                            <td style="font-weight:700;white-space:nowrap;color:<?php echo e($tx->type==='income'?'#10B981':'#EF4444'); ?>">
                                <?php echo e($tx->type==='income'?'+':'-'); ?> Rp <?php echo e(number_format($tx->amount,0,',','.')); ?>

                            </td>
                            <td style="color:#64748B;font-size:.8rem;"><?php echo e(optional($tx->recordedBy)->name??'System'); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" style="text-align:center;color:#94A3B8;padding:3rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 1rem;color:#E2E8F0;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg>
                            <div style="font-weight:700;">Tidak ada transaksi</div>
                        </td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div style="padding:1rem 1.25rem;border-top:1px solid #F1F5F9;"><?php echo e($transactions->links()); ?></div>
        </div>
    </div>

    
    <div x-show="tab==='reimburse'" style="display:none;">
        <div class="table-card">
            <div style="overflow-x:auto;">
                <table class="data-table">
                    <thead><tr><th>Tanggal</th><th>Pemohon</th><th>Deskripsi</th><th>Jumlah</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $reimbursements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php $badge=$r->status_badge; ?>
                        <tr>
                            <td style="color:#64748B;white-space:nowrap;"><?php echo e($r->created_at->format('d M Y')); ?></td>
                            <td style="font-weight:600;"><?php echo e(optional($r->user)->name??'-'); ?></td>
                            <td><?php echo e($r->description); ?></td>
                            <td style="font-weight:700;white-space:nowrap;">Rp <?php echo e(number_format($r->amount,0,',','.')); ?></td>
                            <td><span class="pill" style="background:<?php echo e($badge['bg']); ?>;color:<?php echo e($badge['color']); ?>;"><?php echo e($badge['label']); ?></span></td>
                            <td>
                                <?php if($r->status==='pending'&&in_array(auth()->user()->role,['manager','finance'])): ?>
                                <div style="display:flex;gap:.5rem;">
                                    <form action="<?php echo e(route('internal.finance.reimburse.approve',$r)); ?>" method="POST" onsubmit="return confirm('Setujui?')"><?php echo csrf_field(); ?><input type="hidden" name="action" value="approved"><button type="submit" style="background:#D1FAE5;color:#065F46;border:none;padding:.3rem .7rem;border-radius:.375rem;font-size:.75rem;font-weight:700;cursor:pointer;">Setujui</button></form>
                                    <form action="<?php echo e(route('internal.finance.reimburse.approve',$r)); ?>" method="POST" onsubmit="return confirm('Tolak?')"><?php echo csrf_field(); ?><input type="hidden" name="action" value="rejected"><button type="submit" style="background:#FEE2E2;color:#991B1B;border:none;padding:.3rem .7rem;border-radius:.375rem;font-size:.75rem;font-weight:700;cursor:pointer;">Tolak</button></form>
                                </div>
                                <?php else: ?>
                                    <span style="font-size:.8rem;color:#94A3B8;"><?php echo e($r->status==='approved'?'Oleh: '.optional($r->approvedBy)->name:($r->status==='rejected'?'Ditolak':'-')); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" style="text-align:center;color:#94A3B8;padding:3rem;">Belum ada permintaan reimburse.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div style="padding:1rem 1.25rem;"><?php echo e($reimbursements->links()); ?></div>
        </div>
    </div>
</div>


<div style="margin-top:2rem;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
        <h2 style="font-size:1.25rem;font-weight:800;color:#0F172A;margin:0;">Ringkasan Keuangan</h2>
        <form method="GET" action="<?php echo e(route('internal.finance.index')); ?>" style="display:flex;gap:.75rem;align-items:center;flex-wrap:wrap;" class="no-print">
            <?php if(request('date_from')): ?>
                <input type="hidden" name="date_from" value="<?php echo e(request('date_from')); ?>">
            <?php endif; ?>
            <?php if(request('date_to')): ?>
                <input type="hidden" name="date_to" value="<?php echo e(request('date_to')); ?>">
            <?php endif; ?>
            <?php if(request('type')): ?>
                <input type="hidden" name="type" value="<?php echo e(request('type')); ?>">
            <?php endif; ?>
            <input type="date" name="summary_from" class="filter-input" value="<?php echo e($summaryFrom->format('Y-m-d')); ?>">
            <span style="color:#94A3B8;">—</span>
            <input type="date" name="summary_to" class="filter-input" value="<?php echo e($summaryTo->format('Y-m-d')); ?>">
            <button type="submit" class="btn-add" style="padding:.4rem .875rem;font-size:.8rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                Filter
            </button>
        </form>
    </div>

    <div style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:.75rem;padding:.75rem 1.25rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:.75rem;font-size:.875rem;font-weight:600;color:#1E40AF;" class="no-print">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        <?php echo e($summaryFrom->isoFormat('D MMM YYYY')); ?> – <?php echo e($summaryTo->isoFormat('D MMM YYYY')); ?>

        &nbsp;|&nbsp; Masuk: <span style="color:#059669;">Rp <?php echo e(number_format($periodIncome,0,',','.')); ?></span>
        &nbsp;|&nbsp; Keluar: <span style="color:#DC2626;">Rp <?php echo e(number_format($periodExpense,0,',','.')); ?></span>
        &nbsp;|&nbsp; Saldo: <span style="color:<?php echo e(($periodIncome-$periodExpense)>=0?'#059669':'#DC2626'); ?>;">Rp <?php echo e(number_format(abs($periodIncome-$periodExpense),0,',','.')); ?></span>
    </div>

    <div class="summary-grid">
        
        <div class="summary-card">
            <div class="summary-card-title"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#EA580C" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>Grafik Arus Kas</div>
            <div style="display:flex;gap:1rem;margin-bottom:1rem;">
                <div style="display:flex;align-items:center;gap:.4rem;font-size:.75rem;font-weight:700;color:#059669;"><div style="width:12px;height:12px;border-radius:3px;background:#10B981;"></div>Pemasukan</div>
                <div style="display:flex;align-items:center;gap:.4rem;font-size:.75rem;font-weight:700;color:#DC2626;"><div style="width:12px;height:12px;border-radius:3px;background:#EF4444;"></div>Pengeluaran</div>
            </div>
            <?php $maxVal=collect($cashflowChart)->max(fn($d)=>max($d['income'],$d['expense'])); $maxVal=$maxVal>0?$maxVal:1; ?>
            <div class="chart-bars">
                <?php $__currentLoopData = $cashflowChart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $incH=max(2,round($d['income']/$maxVal*140)); $expH=max(2,round($d['expense']/$maxVal*140)); ?>
                <div class="chart-bar-group" title="<?php echo e($d['date']); ?>">
                    <div class="chart-bar" style="height:<?php echo e($incH); ?>px;background:#10B981;"></div>
                    <div class="chart-bar" style="height:<?php echo e($expH); ?>px;background:#EF4444;"></div>
                    <div class="chart-label"><?php echo e($d['date']); ?></div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-top:1.25rem;">
                <div style="background:#F0FDF4;border-radius:.75rem;padding:.875rem;text-align:center;">
                    <div style="font-size:.75rem;font-weight:700;color:#059669;margin-bottom:.25rem;">PEMASUKAN</div>
                    <div style="font-size:1rem;font-weight:800;color:#065F46;">Rp <?php echo e(number_format($periodIncome,0,',','.')); ?></div>
                </div>
                <div style="background:#FEF2F2;border-radius:.75rem;padding:.875rem;text-align:center;">
                    <div style="font-size:.75rem;font-weight:700;color:#DC2626;margin-bottom:.25rem;">PENGELUARAN</div>
                    <div style="font-size:1rem;font-weight:800;color:#991B1B;">Rp <?php echo e(number_format($periodExpense,0,',','.')); ?></div>
                </div>
            </div>
        </div>

        
        <div class="summary-card">
            <div class="summary-card-title"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#EA580C" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg>Pengeluaran per Kategori</div>
            <?php if($expenseByCategory->isEmpty()): ?>
                <div style="flex:1;display:flex;align-items:center;justify-content:center;color:#94A3B8;font-size:.875rem;">Tidak ada pengeluaran</div>
            <?php else: ?>
                <?php $maxCat=$expenseByCategory->max('total')?:1; ?>
                <div style="flex:1;overflow-y:auto;">
                    <?php $__currentLoopData = $expenseByCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="cat-row">
                        <div style="font-size:.8rem;font-weight:700;color:#0F172A;min-width:80px;max-width:90px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo e($cat->category??'Lainnya'); ?></div>
                        <div class="cat-bar-bg"><div class="cat-bar-fill" style="width:<?php echo e(round($cat->total/$maxCat*100)); ?>%;"></div></div>
                        <div style="font-size:.8rem;font-weight:800;color:#0F172A;white-space:nowrap;min-width:70px;text-align:right;">Rp <?php echo e(number_format($cat->total,0,',','.')); ?></div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>

        
        <div class="summary-card">
            <div class="summary-card-title"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#EA580C" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>Metode Pembayaran</div>
            <?php $pt=$paymentTotal>0?$paymentTotal:1; ?>
            <div class="pay-method">
                <div class="pay-header">
                    <div class="pay-icon" style="color:#8B5CF6;"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M7 7h.01M17 7h.01M7 17h.01M17 17h.01M12 7h.01M12 12h.01M12 17h.01M7 12h.01M17 12h.01"/></svg></div>
                    <div style="font-weight:700;font-size:.875rem;">QRIS</div>
                    <div style="margin-left:auto;font-weight:800;font-size:.875rem;">Rp <?php echo e(number_format($qrisTotal,0,',','.')); ?></div>
                </div>
                <div style="font-size:.75rem;color:#64748B;"><?php echo e($paymentTotal>0?round($qrisTotal/$pt*100):0); ?>% dari total</div>
                <div class="progress-bar"><div class="progress-fill" style="width:<?php echo e($paymentTotal>0?round($qrisTotal/$pt*100):0); ?>%;background:#8B5CF6;"></div></div>
            </div>
            <div class="pay-method">
                <div class="pay-header">
                    <div class="pay-icon" style="color:#EA580C;"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/></svg></div>
                    <div style="font-weight:700;font-size:.875rem;">Tunai</div>
                    <div style="margin-left:auto;font-weight:800;font-size:.875rem;">Rp <?php echo e(number_format($tunaiTotal,0,',','.')); ?></div>
                </div>
                <div style="font-size:.75rem;color:#64748B;"><?php echo e($paymentTotal>0?round($tunaiTotal/$pt*100):0); ?>% dari total</div>
                <div class="progress-bar"><div class="progress-fill" style="width:<?php echo e($paymentTotal>0?round($tunaiTotal/$pt*100):0); ?>%;background:#EA580C;"></div></div>
            </div>
            <?php if($paymentTotal==0): ?>
                <div style="text-align:center;color:#94A3B8;font-size:.85rem;margin-top:.5rem;">Belum ada pembayaran</div>
            <?php endif; ?>
        </div>
    </div>
</div>


<div style="background:#FFF7ED;border:1px solid #FFEDD5;border-radius:1rem;padding:1.5rem;display:flex;align-items:flex-start;gap:1rem;margin-bottom:2rem;" class="no-print">
    <div style="width:40px;height:40px;background:white;border-radius:.75rem;display:flex;align-items:center;justify-content:center;color:#EA580C;flex-shrink:0;box-shadow:0 4px 6px -1px rgba(234,88,12,.1);">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    </div>
    <div style="flex:1;">
        <div style="font-weight:800;font-size:.95rem;color:#0F172A;margin-bottom:.25rem;">Alur Pembayaran Customer Online</div>
        <div style="font-size:.875rem;color:#64748B;line-height:1.6;">
            Pesanan online masuk dengan status <b>Belum Bayar</b>. Kasir harus <b>mengkonfirmasi pembayaran</b> di <b>Riwayat Order</b> (tombol "Bayar"). Setelah dikonfirmasi, transaksi otomatis tercatat sebagai Pemasukan di sini. Pesanan dari POS langsung tercatat otomatis.
        </div>
    </div>
    <a href="<?php echo e(route('internal.orders.index')); ?>" style="padding:.5rem 1rem;background:white;color:#EA580C;border:1px solid #FFEDD5;border-radius:.5rem;font-size:.85rem;font-weight:700;text-decoration:none;display:flex;align-items:center;gap:.4rem;white-space:nowrap;">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        Kelola Order
    </a>
</div>


<div id="addTxModal" class="modal-overlay" style="display:none;">
    <div class="modal-card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
            <div style="font-size:1.25rem;font-weight:800;color:#0F172A;">Catat Transaksi Manual</div>
            <button onclick="closeTxModal()" style="background:#F1F5F9;border:none;width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:1.25rem;color:#64748B;display:flex;align-items:center;justify-content:center;">&times;</button>
        </div>
        <form action="<?php echo e(route('internal.finance.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div class="form-group"><label class="form-label">Tipe *</label><select name="type" class="form-input" required><option value="income">Pemasukan</option><option value="expense">Pengeluaran</option></select></div>
                <div class="form-group"><label class="form-label">Jumlah (Rp) *</label><input type="number" name="amount" class="form-input" required min="1" placeholder="50000"></div>
            </div>
            <div class="form-group"><label class="form-label">Deskripsi *</label><input type="text" name="description" class="form-input" required placeholder="Cth: Penjualan nasi goreng"></div>
            <div class="form-group"><label class="form-label">Kategori</label><input type="text" name="category" class="form-input" placeholder="Penjualan, Operasional, dll"></div>
            <div style="display:flex;gap:1rem;justify-content:flex-end;margin-top:1.5rem;">
                <button type="button" onclick="closeTxModal()" style="background:#F1F5F9;color:#64748B;border:none;padding:.75rem 1.25rem;border-radius:.5rem;font-weight:600;cursor:pointer;">Batal</button>
                <button type="submit" class="btn-add" style="padding:.75rem 1.5rem;">Simpan</button>
            </div>
        </form>
    </div>
</div>


<div id="addRModal" class="modal-overlay" style="display:none;">
    <div class="modal-card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
            <div style="font-size:1.25rem;font-weight:800;color:#0F172A;">Ajukan Reimbursement</div>
            <button onclick="closeRModal()" style="background:#F1F5F9;border:none;width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:1.25rem;color:#64748B;display:flex;align-items:center;justify-content:center;">&times;</button>
        </div>
        <form action="<?php echo e(route('internal.finance.reimburse')); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div class="form-group"><label class="form-label">Deskripsi *</label><input type="text" name="description" class="form-input" required placeholder="Cth: Beli gas memasak"></div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div class="form-group"><label class="form-label">Jumlah (Rp) *</label><input type="number" name="amount" class="form-input" required min="1"></div>
                <div class="form-group"><label class="form-label">Kategori</label><input type="text" name="category" class="form-input" placeholder="Operasional"></div>
            </div>
            <div class="form-group"><label class="form-label">Upload Nota (Opsional)</label><input type="file" name="receipt" class="form-input" accept="image/*,application/pdf" style="background:white;padding:.5rem;"></div>
            <div style="display:flex;gap:1rem;justify-content:flex-end;margin-top:1.5rem;">
                <button type="button" onclick="closeRModal()" style="background:#F1F5F9;color:#64748B;border:none;padding:.75rem 1.25rem;border-radius:.5rem;font-weight:600;cursor:pointer;">Batal</button>
                <button type="submit" class="btn-add" style="padding:.75rem 1.5rem;">Ajukan</button>
            </div>
        </form>
    </div>
</div>

<script>
// ── Buka modal sesuai tab aktif ──────────────────────────────────────────────
function openFinanceModal() {
    // Baca tab aktif dari Alpine component
    const tabDiv = document.querySelector('[x-data]');
    let activeTab = 'transactions';
    if (tabDiv && tabDiv._x_dataStack && tabDiv._x_dataStack[0]) {
        activeTab = tabDiv._x_dataStack[0].tab ?? 'transactions';
    }
    // Fallback: cek tombol tab mana yang active
    if (!tabDiv?._x_dataStack) {
        const activeBtn = document.querySelector('.tab-btn.active');
        if (activeBtn && activeBtn.textContent.trim().includes('Reimburse')) {
            activeTab = 'reimburse';
        }
    }
    if (activeTab === 'reimburse') {
        document.getElementById('addRModal').style.display = 'flex';
    } else {
        document.getElementById('addTxModal').style.display = 'flex';
    }
}

// ── Fungsi buka modal spesifik ───────────────────────────────────────────────
function openTxModal()  { document.getElementById('addTxModal').style.display = 'flex'; }
function openRModal()   { document.getElementById('addRModal').style.display  = 'flex'; }
function closeTxModal() { document.getElementById('addTxModal').style.display = 'none'; }
function closeRModal()  { document.getElementById('addRModal').style.display  = 'none'; }

// ── Tutup modal klik di luar / ESC ───────────────────────────────────────────
document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) m.style.display = 'none'; });
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay').forEach(m => m.style.display = 'none');
    }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.internal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\KANTINVOKASI\resources\views/internal/finance.blade.php ENDPATH**/ ?>