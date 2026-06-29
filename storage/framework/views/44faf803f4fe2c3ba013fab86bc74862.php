
<?php $__env->startSection('title', 'Dashboard Operasional'); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* ── Dashboard Clean & Premium ── */
.stats-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem;margin-bottom:2rem}
@media(max-width:900px){.stats-grid{grid-template-columns:repeat(2,1fr)}}

.stat-card{background:#FFF;border-radius:1rem;padding:1.5rem;border:1px solid #E2E8F0;display:flex;gap:1.25rem;align-items:center;transition:all 0.3s ease;box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);}
.stat-card:hover{box-shadow:0 10px 15px -3px rgba(0,0,0,0.05);transform:translateY(-2px)}
.stat-icon{width:56px;height:56px;border-radius:1rem;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.stat-value{font-size:1.75rem;font-weight:800;line-height:1.2;color:#0F172A}
.stat-label{font-size:.75rem;color:#64748B;font-weight:700;text-transform:uppercase;letter-spacing:.05em}

/* Grid Layout */
.dash-grid{display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;margin-bottom:1.5rem}
@media(max-width:1100px){.dash-grid{grid-template-columns:1fr}}

.card{background:#FFF;border-radius:1rem;border:1px solid #E2E8F0;overflow:hidden;box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);}
.card-header{display:flex;justify-content:space-between;align-items:center;padding:1.25rem 1.5rem;border-bottom:1px solid #F1F5F9;background:#FAFAFA}
.card-title{font-weight:800;font-size:.95rem;color:#0F172A;display:flex;align-items:center;gap:0.5rem}
.card-body{padding:1.5rem}

/* Chart Mockup */
.chart-container { height: 200px; display: flex; align-items: flex-end; gap: 1rem; padding-top: 1rem; }
.chart-bar-group { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 0.5rem; }
.chart-bar-main { width: 100%; max-width: 40px; background: #EA580C; border-radius: 4px 4px 0 0; transition: height 0.6s ease; }
.chart-bar-sub { width: 100%; max-width: 40px; background: #FFEDD5; border-radius: 4px 4px 0 0; position: absolute; bottom: 0; z-index: -1; }
.chart-label { font-size: 0.7rem; color: #94A3B8; font-weight: 700; }

/* AI Panel Redesign */
.ai-panel{background:#FFF7ED;border:1px solid #FFEDD5;border-radius:1rem;padding:1.5rem;color:#0F172A}
.ai-panel-title{font-size:.95rem;font-weight:800;margin-bottom:1.25rem;display:flex;align-items:center;gap:.75rem}
.ai-badge{background:#EA580C;color:white;font-size:.65rem;font-weight:800;padding:.2rem .6rem;border-radius:0.5rem;letter-spacing:.05em}
.ai-alert{padding:1rem;border-radius:.75rem;font-size:.875rem;margin-bottom:0.75rem;line-height:1.5;display:flex;align-items:flex-start;gap:0.75rem;background:white;border:1px solid #FFEDD5}
.ai-alert.danger{border-left:4px solid #EF4444;color:#991B1B}
.ai-alert.warning{border-left:4px solid #F59E0B;color:#92400E}
.ai-alert.info{border-left:4px solid #3B82F6;color:#1E40AF}
.ai-alert.success{border-left:4px solid #10B981;color:#065F46}
.ai-empty{text-align:center;padding:2rem;color:#94A3B8;font-size:.875rem}

/* Tables */
.order-table{width:100%;border-collapse:collapse}
.order-table th{font-size:.75rem;font-weight:700;color:#64748B;text-transform:uppercase;letter-spacing:.05em;padding:1rem .75rem;border-bottom:1px solid #E2E8F0;text-align:left;background:#F8FAFC}
.order-table td{padding:1rem .75rem;font-size:.875rem;border-bottom:1px dashed #E2E8F0;color:#0F172A}
.order-table tr:last-child td{border-bottom:none}
.status-pill{display:inline-flex;align-items:center;padding:.25rem .75rem;border-radius:9999px;font-size:.7rem;font-weight:800;text-transform:uppercase}

/* Mini Grid */
.mini-grid{display:grid;grid-template-columns:1fr 1fr;gap:0.75rem}
.mini-card{background:#F8FAFC;border-radius:.75rem;padding:1rem;border:1px solid #E2E8F0}
.mini-label{font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.5rem;color:#64748B}
.mini-value{font-size:1.5rem;font-weight:800;color:#0F172A}

/* Top Menus */
.top-menu-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:1rem}
.top-menu-item{text-align:center;background:#F8FAFC;border-radius:1rem;padding:1.25rem 1rem;border:1px solid #E2E8F0;transition:0.2s}
.top-menu-item:hover{background:white;box-shadow:0 4px 12px rgba(0,0,0,0.05);transform:translateY(-2px)}
.top-menu-rank{width:32px;height:32px;background:#EA580C;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;margin:0 auto 1rem;font-size:0.85rem}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>


<div class="stats-grid">
    <?php
    $statsConfig = [
        [
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>',
            'label'=>'Order Hari Ini',
            'value'=>$stats['orders_today'],
            'icon_bg'=>'#FFEDD5',
            'icon_color'=>'#EA580C',
            'format'=>'number'
        ],
        [
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>',
            'label'=>'Antrian Dapur',
            'value'=>$stats['pending_orders'],
            'icon_bg'=>'#FEF3C7',
            'icon_color'=>'#D97706',
            'format'=>'number'
        ],
        [
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="22"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>',
            'label'=>'Pendapatan Hari Ini',
            'value'=>$stats['income_today'],
            'icon_bg'=>'#DCFCE7',
            'icon_color'=>'#16A34A',
            'format'=>'currency'
        ],
    ];
    ?>

    <?php $__currentLoopData = $statsConfig; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="stat-card">
        <div class="stat-icon" style="background:<?php echo e($s['icon_bg']); ?>; color:<?php echo e($s['icon_color']); ?>">
            <?php echo $s['icon']; ?>

        </div>
        <div>
            <div class="stat-value">
                <?php if($s['format']==='currency'): ?>Rp <?php echo e(number_format($s['value'],0,',','.')); ?>

                <?php else: ?><?php echo e($s['value']); ?>

                <?php endif; ?>
            </div>
            <div class="stat-label"><?php echo e($s['label']); ?></div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<div class="dash-grid">

    
    <div style="display:flex;flex-direction:column;gap:1.5rem">

        
        <div class="card">
            <div class="card-header">
                <span class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color:#EA580C"><line x1="12" y1="20" x2="12" y2="10"></line><line x1="18" y1="20" x2="18" y2="4"></line><line x1="6" y1="20" x2="6" y2="16"></line></svg>
                    Pendapatan 7 Hari Terakhir
                </span>
                <span style="font-size:.8rem;color:#64748B;font-weight:700"><?php echo e(now()->format('F Y')); ?></span>
            </div>
            <div class="card-body">
                <?php $maxRevenue = max(array_column($revenueChart, 'revenue') ?: [1]); ?>
                <div class="chart-container">
                    <?php $__currentLoopData = $revenueChart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $point): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $h = $maxRevenue > 0 ? max(8, round($point['revenue'] / $maxRevenue * 160)) : 8; ?>
                    <div class="chart-bar-group" title="Rp <?php echo e(number_format($point['revenue'],0,',','.')); ?> — <?php echo e($point['orders']); ?> order">
                        <div class="chart-bar-main" style="height:<?php echo e($h); ?>px"></div>
                        <span class="chart-label"><?php echo e($point['date']); ?></span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div style="display:flex;justify-content:space-between;margin-top:1.5rem;font-size:.75rem;color:#94A3B8;font-weight:700">
                    <?php $__currentLoopData = $revenueChart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span><?php echo e($p['orders']); ?> ord</span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>

        
        <div class="card">
            <div class="card-header">
                <span class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color:#EA580C"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    Pesanan Terbaru
                </span>
                <a href="<?php echo e(route('internal.orders.index')); ?>" style="font-size:.8rem;color:#EA580C;text-decoration:none;font-weight:800;display:flex;align-items:center;gap:0.25rem">
                    LIHAT SEMUA
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"></path></svg>
                </a>
            </div>
            <div class="card-body" style="padding:0">
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>No. Pesanan</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                        $statusColor = match($order->status) {
                            'menunggu'  => ['#FFFBEB','#D97706'],
                            'diproses'  => ['#EFF6FF','#2563EB'],
                            'siap'      => ['#ECFDF5','#059669'],
                            'selesai'   => ['#F8FAFC','#64748B'],
                            'dibatalkan'=> ['#FEF2F2','#DC2626'],
                            default     => ['#F8FAFC','#64748B'],
                        };
                        ?>
                        <tr>
                            <td><span style="font-weight:800;color:#0F172A"><?php echo e($order->order_number); ?></span></td>
                            <td style="font-weight:500"><?php echo e($order->customer_name); ?></td>
                            <td><span style="font-weight:800">Rp <?php echo e(number_format($order->total,0,',','.')); ?></span></td>
                            <td><span class="status-pill" style="background:<?php echo e($statusColor[0]); ?>;color:<?php echo e($statusColor[1]); ?>"><?php echo e($order->status); ?></span></td>
                            <td style="color:#94A3B8;font-weight:600"><?php echo e($order->created_at->format('H:i')); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5" style="text-align:center;color:#94A3B8;padding:3rem;font-weight:600">Belum ada pesanan hari ini</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
    <div style="display:flex;flex-direction:column;gap:1.5rem">

        
        <div class="ai-panel">
            <div class="ai-panel-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color:#EA580C"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8z"/><path d="M12 7v5l3 3"/></svg>
                AI Insights
                <span class="ai-badge">ACTIVE</span>
            </div>

            <?php if(count($aiAlerts) > 0): ?>
                <?php $__currentLoopData = $aiAlerts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="ai-alert <?php echo e($alert['level']); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <?php echo e($alert['message']); ?>

                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>

            <?php if(count($aiInsights) > 0): ?>
                <?php $__currentLoopData = $aiInsights; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $insight): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="ai-alert <?php echo e($insight['type']); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><path d="M9.663 17h4.674"></path><path d="M10 20h4"></path><path d="M12 3a7 7 0 0 0-7 7c0 1.692.706 3.22 1.841 4.305C8.422 15.802 9 17.619 9 19h6c0-1.381.578-3.198 2.159-4.695A6.974 6.974 0 0 0 19 10a7 7 0 0 0-7-7z"></path></svg>
                    <?php echo e($insight['message']); ?>

                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>

            <?php if(count($aiAlerts) === 0 && count($aiInsights) === 0): ?>
            <div class="ai-empty">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color:#E2E8F0;margin-bottom:1rem"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"></path><path d="m9 12 2 2 4-4"></path></svg>
                <div style="font-weight:700">Sistem Berjalan Normal</div>
                <div style="font-size:0.8rem;margin-top:0.25rem">Tidak ada rekomendasi saat ini</div>
            </div>
            <?php endif; ?>
        </div>

        
        <div class="card">
            <div class="card-header">
                <span class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color:#EA580C"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"></path><path d="M7 2v20"></path><path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"></path></svg>
                    Status Dapur
                </span>
                <a href="<?php echo e(route('internal.kitchen')); ?>" style="font-size:.8rem;color:#EA580C;text-decoration:none;font-weight:800">KDS →</a>
            </div>
            <div class="card-body">
                <div class="mini-grid">
                    <div class="mini-card" style="border-left:3px solid #D97706">
                        <div class="mini-label">Menunggu</div>
                        <div class="mini-value" style="color:#D97706"><?php echo e($kitchenAgent['pending'] ?? 0); ?></div>
                    </div>
                    <div class="mini-card" style="border-left:3px solid #2563EB">
                        <div class="mini-label">Dimasak</div>
                        <div class="mini-value" style="color:#2563EB"><?php echo e($kitchenAgent['in_progress'] ?? 0); ?></div>
                    </div>
                    <div class="mini-card" style="border-left:3px solid #059669">
                        <div class="mini-label">Siap</div>
                        <div class="mini-value" style="color:#059669"><?php echo e($kitchenAgent['ready'] ?? 0); ?></div>
                    </div>
                    <div class="mini-card" style="border-left:3px solid #DC2626">
                        <div class="mini-label">Late</div>
                        <div class="mini-value" style="color:#DC2626"><?php echo e($kitchenAgent['overdue_count'] ?? 0); ?></div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="card">
            <div class="card-header">
                <span class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color:#EA580C"><rect width="20" height="14" x="2" y="5" rx="2"></rect><line x1="2" x2="22" y1="10" y2="10"></line></svg>
                    Keuangan Hari Ini
                </span>
                <a href="<?php echo e(route('internal.finance.index')); ?>" style="font-size:.8rem;color:#EA580C;text-decoration:none;font-weight:800">DETAIL →</a>
            </div>
            <div class="card-body">
                <div style="display:flex;flex-direction:column;gap:1rem">
                    <div style="display:flex;justify-content:space-between;align-items:center">
                        <span style="font-size:.875rem;color:#64748B;font-weight:600">Pemasukan</span>
                        <span style="font-weight:800;color:#16A34A">Rp <?php echo e(number_format($financeAgent['income_today']??0,0,',','.')); ?></span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center">
                        <span style="font-size:.875rem;color:#64748B;font-weight:600">Pengeluaran</span>
                        <span style="font-weight:800;color:#DC2626">Rp <?php echo e(number_format($financeAgent['expense_today']??0,0,',','.')); ?></span>
                    </div>
                    <div style="border-top:1px dashed #E2E8F0;padding-top:1rem;display:flex;justify-content:space-between;align-items:center">
                        <span style="font-size:.875rem;font-weight:800;color:#0F172A">Saldo Bersih</span>
                        <span style="font-weight:900;font-size:1.1rem;color:<?php echo e(($financeAgent['balance_today']??0) >= 0 ? '#0F172A' : '#DC2626'); ?>">
                            Rp <?php echo e(number_format($financeAgent['balance_today']??0,0,',','.')); ?>

                        </span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


<?php if(!empty($analyticsAgent['top_menus'])): ?>
<div class="card" style="margin-top:1.5rem">
    <div class="card-header">
        <span class="card-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color:#EA580C"><path d="M6 9 12 15 18 9"></path></svg>
            Menu Terlaris Minggu Ini
        </span>
    </div>
    <div class="card-body">
        <div class="top-menu-grid">
            <?php $__currentLoopData = $analyticsAgent['top_menus']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="top-menu-item">
                <div class="top-menu-rank"><?php echo e($i + 1); ?></div>
                <div style="font-weight:800;font-size:.875rem;color:#0F172A;margin-bottom:.5rem"><?php echo e($menu['name']); ?></div>
                <div style="font-size:.8rem;color:#EA580C;font-weight:800;margin-bottom:0.25rem"><?php echo e($menu['qty']); ?> porsi</div>
                <div style="font-size:.75rem;color:#94A3B8;font-weight:600">Rp <?php echo e(number_format($menu['revenue'],0,',','.')); ?></div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
// Auto-refresh every 60 seconds (fallback)
setTimeout(() => location.reload(), 60000);

const pusher = new Pusher('<?php echo e(config("broadcasting.connections.reverb.key")); ?>', {
    wsHost: window.location.hostname,
    wsPort: <?php echo e(config("broadcasting.connections.reverb.options.port", 8080)); ?>,
    forceTLS: false,
    disableStats: true,
    cluster: 'mt1'
});

// Admin channel for all system updates
const channel = pusher.subscribe('admin-updates');
channel.bind('kitchen.status.updated', function(data) {
    location.reload();
});
channel.bind('finance.updated', function(data) {
    location.reload();
});
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.internal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\KANTINVOKASI\resources\views/internal/dashboard.blade.php ENDPATH**/ ?>