<?php $__env->startSection('title', 'Manajemen Inventori'); ?>

<?php $__env->startSection('content'); ?>
<style>
/* ── Stats Grid ── */
.inv-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.25rem;
    margin-bottom: 2rem;
}
@media(max-width: 900px) { .inv-stats { grid-template-columns: repeat(2, 1fr); } }

.inv-card {
    background: var(--bg-card);
    border-radius: var(--radius-lg);
    padding: 1.375rem;
    border: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 1.125rem;
    box-shadow: var(--shadow-sm);
    transition: all 0.2s ease;
}
.inv-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-md);
}
.inv-card.clickable {
    cursor: pointer;
}
.inv-card.clickable:hover {
    border-color: var(--primary);
}
.inv-icon {
    width: 48px; height: 48px;
    border-radius: var(--radius-md);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.inv-stat-value { font-size: 1.625rem; font-weight: 900; line-height: 1.1; }
.inv-stat-label { font-size: 0.7rem; color: var(--text-muted); font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; margin-top: 2px; }

/* ── Tabs ── */
.tabs-nav {
    display: flex;
    border-bottom: 2px solid var(--border-light);
    margin-bottom: 1.75rem;
    gap: 0;
}
.tab-link {
    padding: 0.75rem 1.25rem;
    font-size: 0.8rem;
    font-weight: 800;
    background: none;
    border: none;
    border-bottom: 2px solid transparent;
    cursor: pointer;
    color: var(--text-muted);
    transition: 0.15s;
    font-family: inherit;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: -2px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.tab-link:hover  { color: var(--text-dark); }
.tab-link.active { color: var(--primary); border-bottom-color: var(--primary); }

/* ── Filter bar ── */
.filter-bar {
    display: flex;
    gap: 0.625rem;
    flex-wrap: wrap;
    align-items: center;
    margin-bottom: 1.25rem;
}
.filter-pill {
    padding: 0.35rem 0.875rem;
    border-radius: var(--radius-full);
    border: 1.5px solid var(--border);
    background: white;
    font-size: 0.78rem;
    font-weight: 700;
    color: var(--text-muted);
    cursor: pointer;
    transition: 0.15s;
    font-family: inherit;
}
.filter-pill:hover  { border-color: var(--primary); color: var(--primary); }
.filter-pill.active { background: var(--primary); color: white; border-color: var(--primary); }

/* ── Table ── */
.table-wrap-inv {
    background: var(--bg-card);
    border-radius: var(--radius-lg);
    border: 1px solid var(--border);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}
.data-table { width: 100%; border-collapse: collapse; }
.data-table th {
    font-size: 0.6875rem; font-weight: 800; color: var(--text-muted);
    text-transform: uppercase; letter-spacing: 0.06em;
    padding: 0.875rem 1.25rem; border-bottom: 1px solid var(--border);
    background: #FAFAFA; text-align: left; white-space: nowrap;
}
.data-table td {
    padding: 0.875rem 1.25rem; font-size: 0.875rem;
    border-bottom: 1px solid var(--border-light);
    color: var(--text-dark); vertical-align: middle;
}
.data-table tr:last-child td { border-bottom: none; }
.data-table tbody tr:hover { background: #FAFAFA; }

.stock-meter { height: 6px; border-radius: 10px; background: var(--border-light); overflow: hidden; width: 90px; }
.stock-fill  { height: 100%; border-radius: 10px; transition: width 0.5s ease; }

.btn-action {
    padding: 0.375rem 0.75rem;
    border-radius: var(--radius-sm);
    font-size: 0.75rem; font-weight: 700;
    border: 1.5px solid transparent;
    cursor: pointer; transition: 0.15s;
    display: inline-flex; align-items: center; gap: 0.35rem;
    font-family: inherit;
}
.btn-restock { background: var(--primary-soft);  color: var(--primary);      border-color: var(--primary-light); }
.btn-restock:hover { background: var(--primary); color: white; }
.btn-edit    { background: var(--bg-subtle);     color: var(--text-body);    border-color: var(--border); }
.btn-edit:hover    { background: var(--text-dark); color: white; border-color: var(--text-dark); }
.btn-delete  { background: var(--danger-bg);     color: var(--danger-text);  border-color: #FEE2E2; }
.btn-delete:hover  { background: var(--danger); color: white; }

/* ── Modals ── */
.modal-backdrop {
    position: fixed; inset: 0;
    background: rgba(15,23,42,0.45);
    backdrop-filter: blur(8px);
    z-index: 1000;
    display: flex; align-items: center; justify-content: center;
    padding: 1rem;
}
.modal-box {
    background: var(--bg-card);
    border-radius: var(--radius-xl);
    padding: 2rem;
    width: 100%; max-width: 540px;
    max-height: 90vh; overflow-y: auto;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.2);
}
.modal-title  { font-size: 1.125rem; font-weight: 900; color: var(--text-dark); margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-light); }
.field-label  { display: block; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-muted); margin-bottom: 0.4rem; }
.field-input  {
    width: 100%; padding: 0.625rem 0.875rem;
    border: 1.5px solid var(--border); border-radius: var(--radius-md);
    font-size: 0.9rem; font-family: inherit; color: var(--text-dark);
    background: var(--bg-card); transition: 0.15s;
}
.field-input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-light); }
.modal-footer { display: flex; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border-light); }
</style>


<div x-data="inventoryApp()">

    
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1.75rem; flex-wrap:wrap; gap:1rem;">
        <div>
            <h1 class="page-title">Inventori Bahan</h1>
            <p class="page-sub">Kelola ketersediaan bahan baku operasional</p>
        </div>
        <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
            <button @click="openModal('purchaseModal')" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                Catat Pembelian
            </button>
            <button @click="openModal('addModal')" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Item Baru
            </button>
        </div>
    </div>

    
    <div class="inv-stats">
        
        <div class="inv-card" @click="clearStockFilter()" style="cursor:pointer;">
            <div class="inv-icon" style="background:var(--info-bg); color:var(--info);">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
            </div>
            <div>
                <div class="inv-stat-value"><?php echo e($stats['total']); ?></div>
                <div class="inv-stat-label">Total Jenis</div>
            </div>
        </div>

        
        <div class="inv-card clickable" :class="stockFilter === 'low' ? 'active' : ''"
             @click="setStockFilter('low')"
             style="border-color: var(--warning);">
            <div class="inv-icon" style="background:var(--warning-bg); color:var(--warning);">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
            </div>
            <div>
                <div class="inv-stat-value" style="color:var(--warning);"><?php echo e($stats['low']); ?></div>
                <div class="inv-stat-label">Stok Menipis</div>
            </div>
        </div>

        
        <div class="inv-card clickable" :class="stockFilter === 'empty' ? 'active' : ''"
             @click="setStockFilter('empty')"
             style="border-color: var(--danger);">
            <div class="inv-icon" style="background:var(--danger-bg); color:var(--danger);">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </div>
            <div>
                <div class="inv-stat-value" style="color:var(--danger);"><?php echo e($stats['critical']); ?></div>
                <div class="inv-stat-label">Stok Habis</div>
            </div>
        </div>

        
        <div class="inv-card">
            <div class="inv-icon" style="background:var(--success-bg); color:var(--success);">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div>
                <div class="inv-stat-value" style="font-size:1.1rem; color:var(--success);">Rp <?php echo e(number_format($stats['value'],0,',','.')); ?></div>
                <div class="inv-stat-label">Nilai Aset</div>
            </div>
        </div>
    </div>

    
    <template x-if="stockFilter">
        <div style="display:flex; align-items:center; gap:0.75rem; padding:0.75rem 1.125rem; background:var(--warning-bg); border:1px solid #FDE68A; border-radius:var(--radius-md); margin-bottom:1.25rem; font-size:0.875rem; font-weight:600; color:var(--warning-text);">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
            <span x-text="stockFilter === 'low' ? 'Menampilkan item dengan stok menipis' : 'Menampilkan item dengan stok habis'"></span>
            <button @click="clearStockFilter()" style="margin-left:auto; background:none; border:none; cursor:pointer; font-weight:800; color:var(--warning-text); font-size:0.875rem; display:flex; align-items:center; gap:0.3rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                Hapus Filter
            </button>
        </div>
    </template>

    
    <div class="tabs-nav">
        <button class="tab-link" :class="{active: tab === 'stock'}" @click="tab = 'stock'">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="M12 22V12"/></svg>
            Stok Saat Ini
        </button>
        <button class="tab-link" :class="{active: tab === 'movements'}" @click="tab = 'movements'">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            Riwayat Log
        </button>
        <button class="tab-link" :class="{active: tab === 'purchases'}" @click="tab = 'purchases'">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
            Daftar Belanja
        </button>
    </div>

    
    <div x-show="tab === 'stock'">

        
        <div class="filter-bar">
            <button class="filter-pill" :class="{active: activeCategory === ''}" @click="activeCategory = ''">Semua</button>
            <?php $__currentLoopData = $items->keys(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <button class="filter-pill" :class="{active: activeCategory === '<?php echo e($cat); ?>'}" @click="activeCategory = '<?php echo e($cat); ?>'"><?php echo e($cat); ?></button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="table-wrap-inv">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Level</th>
                        <th>Min. Stok</th>
                        <th>Harga / Unit</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $allItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $ratio = $item->minimum_stock > 0 ? $item->stock / $item->minimum_stock : 2;
                        $pct   = min(100, round($ratio * 100));
                        $color = $ratio <= 0 ? 'var(--danger)' : ($ratio <= 1 ? 'var(--warning)' : 'var(--success)');
                        $statusClass = $ratio <= 0 ? 'badge-danger' : ($ratio <= 1 ? 'badge-warning' : 'badge-success');
                        $statusText  = $ratio <= 0 ? 'Habis' : ($ratio <= 1 ? 'Menipis' : 'Normal');
                    ?>
                    <tr
                        x-show="
                            (activeCategory === '' || '<?php echo e($item->category); ?>' === activeCategory) &&
                            (stockFilter === '' ||
                             (stockFilter === 'low'   && <?php echo e($item->stock); ?> > 0 && <?php echo e($item->stock); ?> <= <?php echo e($item->minimum_stock); ?>) ||
                             (stockFilter === 'empty' && <?php echo e($item->stock); ?> <= 0))
                        ">
                        <td>
                            <div style="font-weight:800;"><?php echo e($item->name); ?></div>
                            <div style="font-size:0.75rem; color:var(--text-faint); font-weight:600;"><?php echo e($item->supplier_name ?? '—'); ?></div>
                        </td>
                        <td>
                            <span class="badge badge-muted"><?php echo e($item->category); ?></span>
                        </td>
                        <td>
                            <div style="font-weight:900; color:<?php echo e($color); ?>; font-size:1.05rem;"><?php echo e($item->stock); ?></div>
                            <div style="font-size:0.7rem; color:var(--text-faint); font-weight:700;"><?php echo e(strtoupper($item->unit)); ?></div>
                        </td>
                        <td>
                            <div style="display:flex; align-items:center; gap:0.625rem;">
                                <div class="stock-meter">
                                    <div class="stock-fill" style="width:<?php echo e($pct); ?>%; background:<?php echo e($color); ?>;"></div>
                                </div>
                                <span class="badge <?php echo e($statusClass); ?>" style="font-size:0.6rem;"><?php echo e($statusText); ?></span>
                            </div>
                        </td>
                        <td style="font-weight:700; color:var(--text-muted);"><?php echo e($item->minimum_stock); ?> <?php echo e($item->unit); ?></td>
                        <td style="font-weight:800;">Rp <?php echo e(number_format($item->price_per_unit,0,',','.')); ?></td>
                        <td>
                            <div style="display:flex; gap:0.4rem; justify-content:flex-end; flex-wrap:wrap;">
                                <button class="btn-action btn-restock"
                                    @click="openRestockModal(<?php echo e($item->id); ?>, '<?php echo e(addslashes($item->name)); ?>', '<?php echo e($item->unit); ?>', <?php echo e($item->stock); ?>)"
                                    title="Tambah Stok">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M5 15l7 7 7-7"/></svg>
                                    Restock
                                </button>
                                <button class="btn-action btn-edit"
                                    @click="openEditModal(<?php echo e($item->id); ?>, '<?php echo e(addslashes($item->name)); ?>', '<?php echo e($item->category); ?>', <?php echo e($item->stock); ?>, '<?php echo e($item->unit); ?>', <?php echo e($item->minimum_stock); ?>, <?php echo e($item->price_per_unit); ?>, '<?php echo e(addslashes($item->supplier_name ?? '')); ?>', '<?php echo e(addslashes($item->notes ?? '')); ?>')"
                                    title="Edit Item">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                                    Edit
                                </button>
                                <button class="btn-action btn-delete"
                                    @click="deleteItem(<?php echo e($item->id); ?>, '<?php echo e(addslashes($item->name)); ?>')"
                                    title="Hapus">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" style="text-align:center; padding:4rem; color:var(--text-faint); font-weight:600;">Belum ada data stok</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <div x-show="tab === 'movements'">
        <div class="table-wrap-inv">
            <table class="data-table">
                <thead>
                    <tr><th>Waktu</th><th>Item</th><th>Tipe</th><th>Qty</th><th>Sisa Akhir</th><th>Alasan</th></tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $movements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td style="font-size:0.8rem; color:var(--text-faint); font-weight:700; white-space:nowrap;"><?php echo e($mv->created_at->format('d M, H:i')); ?></td>
                        <td style="font-weight:800;"><?php echo e($mv->inventoryItem?->name ?? 'Dihapus'); ?></td>
                        <td>
                            <span class="badge <?php echo e($mv->type === 'in' ? 'badge-success' : 'badge-danger'); ?>">
                                <?php echo e($mv->type === 'in' ? 'MASUK' : 'KELUAR'); ?>

                            </span>
                        </td>
                        <td style="font-weight:900; color:<?php echo e($mv->type === 'in' ? 'var(--success)' : 'var(--danger)'); ?>;">
                            <?php echo e($mv->type === 'in' ? '+' : '-'); ?><?php echo e($mv->quantity); ?>

                        </td>
                        <td style="font-weight:700; color:var(--text-muted);"><?php echo e($mv->stock_after); ?></td>
                        <td style="font-size:0.85rem;"><?php echo e($mv->reason_label ?? $mv->reason); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" style="text-align:center; padding:4rem; color:var(--text-faint); font-weight:600;">Belum ada riwayat pergerakan</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <div x-show="tab === 'purchases'">
        <div class="table-wrap-inv">
            <table class="data-table">
                <thead>
                    <tr><th>Tanggal</th><th>Item</th><th>Supplier</th><th>Qty</th><th>Total Biaya</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php $badge = $p->status_badge ?? ['bg'=>'#F1F5F9','color'=>'#64748B','label'=>$p->status]; ?>
                    <tr>
                        <td style="font-size:0.8rem; color:var(--text-faint); font-weight:700;"><?php echo e($p->created_at->format('d/m/Y')); ?></td>
                        <td style="font-weight:800;"><?php echo e($p->inventoryItem?->name ?? 'Dihapus'); ?></td>
                        <td style="color:var(--text-muted);"><?php echo e($p->supplier_name ?? '—'); ?></td>
                        <td style="font-weight:700;"><?php echo e($p->quantity); ?></td>
                        <td style="font-weight:900; color:var(--success);">Rp <?php echo e(number_format($p->total_cost,0,',','.')); ?></td>
                        <td><span class="badge" style="background:<?php echo e($badge['bg']); ?>; color:<?php echo e($badge['color']); ?>;"><?php echo e(strtoupper($badge['label'])); ?></span></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" style="text-align:center; padding:4rem; color:var(--text-faint); font-weight:600;">Belum ada data belanja</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>




<div id="addModal" class="modal-backdrop" style="display:none;" onclick="if(event.target===this) closeModal('addModal')">
    <div class="modal-box">
        <div class="modal-title">Tambah Item Baru</div>
        <form action="<?php echo e(route('internal.inventory.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                <div style="grid-column:span 2;">
                    <label class="field-label">Nama Item</label>
                    <input type="text" name="name" class="field-input" required placeholder="Cth: Ayam Fillet">
                </div>
                <div>
                    <label class="field-label">Kategori</label>
                    <select name="category" class="field-input" required>
                        <option>Bahan Baku</option>
                        <option>Bumbu</option>
                        <option>Minuman</option>
                        <option>Packaging</option>
                        <option>Lain-lain</option>
                    </select>
                </div>
                <div>
                    <label class="field-label">Satuan</label>
                    <input type="text" name="unit" class="field-input" required placeholder="kg, pcs, liter">
                </div>
                <div>
                    <label class="field-label">Stok Awal</label>
                    <input type="number" name="stock" class="field-input" value="0" min="0" step="0.01">
                </div>
                <div>
                    <label class="field-label">Min. Stok</label>
                    <input type="number" name="minimum_stock" class="field-input" value="5" min="0">
                </div>
                <div>
                    <label class="field-label">Harga per Unit</label>
                    <input type="number" name="price_per_unit" class="field-input" required value="0">
                </div>
                <div>
                    <label class="field-label">Supplier</label>
                    <input type="text" name="supplier_name" class="field-input" placeholder="Opsional">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('addModal')" class="btn btn-secondary" style="flex:1;">Batal</button>
                <button type="submit" class="btn btn-primary" style="flex:1;">Simpan Item</button>
            </div>
        </form>
    </div>
</div>


<div id="editModal" class="modal-backdrop" style="display:none;" onclick="if(event.target===this) closeModal('editModal')">
    <div class="modal-box">
        <div class="modal-title">Edit Item Inventori</div>
        <form id="editForm" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                <div style="grid-column:span 2;">
                    <label class="field-label">Nama Item</label>
                    <input type="text" id="edit_name" name="name" class="field-input" required>
                </div>
                <div>
                    <label class="field-label">Kategori</label>
                    <select id="edit_category" name="category" class="field-input" required>
                        <option>Bahan Baku</option>
                        <option>Bumbu</option>
                        <option>Minuman</option>
                        <option>Packaging</option>
                        <option>Lain-lain</option>
                    </select>
                </div>
                <div>
                    <label class="field-label">Satuan</label>
                    <input type="text" id="edit_unit" name="unit" class="field-input" required>
                </div>
                <div>
                    <label class="field-label">Stok Saat Ini</label>
                    <input type="number" id="edit_stock" name="stock" class="field-input" min="0" step="0.01">
                </div>
                <div>
                    <label class="field-label">Min. Stok</label>
                    <input type="number" id="edit_minimum_stock" name="minimum_stock" class="field-input" min="0">
                </div>
                <div>
                    <label class="field-label">Harga per Unit</label>
                    <input type="number" id="edit_price_per_unit" name="price_per_unit" class="field-input" min="0">
                </div>
                <div>
                    <label class="field-label">Supplier</label>
                    <input type="text" id="edit_supplier_name" name="supplier_name" class="field-input">
                </div>
                <div style="grid-column:span 2;">
                    <label class="field-label">Alasan Perubahan Stok <span style="font-weight:500; color:var(--text-faint);">(opsional)</span></label>
                    <input type="text" name="reason" class="field-input" placeholder="Cth: Koreksi manual, Stok opname">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('editModal')" class="btn btn-secondary" style="flex:1;">Batal</button>
                <button type="submit" class="btn btn-primary" style="flex:1;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>


<div id="restockModal" class="modal-backdrop" style="display:none;" onclick="if(event.target===this) closeModal('restockModal')">
    <div class="modal-box" style="max-width:420px;">
        <div class="modal-title">Tambah Stok Cepat</div>
        <p id="restockItemName" style="font-size:0.875rem; color:var(--text-muted); margin-bottom:1.25rem; margin-top:-0.75rem;"></p>
        <form id="restockForm" method="POST">
            <?php echo csrf_field(); ?>
            <div>
                <label class="field-label">Jumlah yang Ditambah</label>
                <input type="number" name="quantity" class="field-input" required step="0.01" value="10">
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('restockModal')" class="btn btn-secondary" style="flex:1;">Batal</button>
                <button type="submit" class="btn btn-primary" style="flex:1;">Konfirmasi</button>
            </div>
        </form>
    </div>
</div>


<div id="purchaseModal" class="modal-backdrop" style="display:none;" onclick="if(event.target===this) closeModal('purchaseModal')">
    <div class="modal-box">
        <div class="modal-title">Catat Pembelian Bahan</div>
        <form action="<?php echo e(route('internal.inventory.purchase')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div style="display:flex; flex-direction:column; gap:1rem;">
                <div>
                    <label class="field-label">Item Inventori</label>
                    <select name="inventory_item_id" class="field-input" required>
                        <option value="">-- Pilih Item --</option>
                        <?php $__currentLoopData = $allItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($item->id); ?>"><?php echo e($item->name); ?> (stok: <?php echo e($item->stock); ?> <?php echo e($item->unit); ?>)</option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="field-label">Nama Supplier</label>
                    <input type="text" name="supplier_name" class="field-input" required placeholder="Cth: Supplier Beras Utama">
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div>
                        <label class="field-label">Jumlah Beli</label>
                        <input type="number" name="quantity" class="field-input" required step="0.01" min="0.1">
                    </div>
                    <div>
                        <label class="field-label">Harga Beli / Unit</label>
                        <input type="number" name="unit_price" class="field-input" required min="0">
                    </div>
                </div>
                <div style="background:var(--primary-soft); padding:0.875rem 1rem; border-radius:var(--radius-md); border:1px solid var(--primary-light); font-size:0.8rem; color:var(--primary); font-weight:600; display:flex; gap:0.5rem; align-items:flex-start;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    Transaksi ini otomatis masuk ke laporan pengeluaran keuangan.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('purchaseModal')" class="btn btn-secondary" style="flex:1;">Batal</button>
                <button type="submit" class="btn btn-success" style="flex:1;">Catat Belanja</button>
            </div>
        </form>
    </div>
</div>


<form id="deleteForm" method="POST" style="display:none;"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?></form>

<script>
/* ── Modal helpers ── */
function openModal(id)  { document.getElementById(id).style.display = 'flex'; }
function closeModal(id) { document.getElementById(id).style.display = 'none'; }

/* ── Edit modal — mengisi semua field sekaligus ── */
function openEditModalFn(id, name, category, stock, unit, minStock, price, supplier, notes) {
    const form = document.getElementById('editForm');
    form.action = `/internal/inventory/${id}`;

    document.getElementById('edit_name').value          = name;
    document.getElementById('edit_unit').value          = unit;
    document.getElementById('edit_stock').value         = stock;
    document.getElementById('edit_minimum_stock').value = minStock;
    document.getElementById('edit_price_per_unit').value = price;
    document.getElementById('edit_supplier_name').value = supplier;

    // Set dropdown kategori
    const sel = document.getElementById('edit_category');
    for (let i = 0; i < sel.options.length; i++) {
        if (sel.options[i].value === category) { sel.selectedIndex = i; break; }
    }
    // Kalau kategori tidak ada di list, tambahkan
    if (sel.value !== category) {
        const opt = new Option(category, category, true, true);
        sel.add(opt);
    }

    openModal('editModal');
}

/* ── Restock modal ── */
function openRestockModalFn(id, name, unit, current) {
    document.getElementById('restockForm').action       = `/internal/inventory/${id}/restock`;
    document.getElementById('restockItemName').innerText = `${name} — Stok saat ini: ${current} ${unit}`;
    openModal('restockModal');
}

/* ── Delete ── */
function deleteItemFn(id, name) {
    if (confirm(`Yakin ingin menghapus item "${name}"? Data riwayat stok tetap tersimpan.`)) {
        const f   = document.getElementById('deleteForm');
        f.action  = `/internal/inventory/${id}`;
        f.submit();
    }
}

/* ── Alpine data ── */
function inventoryApp() {
    return {
        tab:            'stock',
        activeCategory: '',
        stockFilter:    '',

        setStockFilter(type) {
            /* Toggle: klik dua kali = hapus filter */
            this.stockFilter    = this.stockFilter === type ? '' : type;
            this.activeCategory = '';
            this.tab            = 'stock';
        },

        clearStockFilter() {
            this.stockFilter = '';
        },

        openEditModal(id, name, category, stock, unit, minStock, price, supplier, notes) {
            openEditModalFn(id, name, category, stock, unit, minStock, price, supplier, notes);
        },

        openRestockModal(id, name, unit, current) {
            openRestockModalFn(id, name, unit, current);
        },

        deleteItem(id, name) {
            deleteItemFn(id, name);
        },
    };
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.internal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\KANTINVOKASI\resources\views/internal/inventory.blade.php ENDPATH**/ ?>