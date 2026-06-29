@extends('layouts.customer')
@section('title', 'Riwayat Pesanan')

@section('content')
<style>
.history-container { max-width: 800px; margin: 2rem auto; padding: 0 1rem; }
.page-title    { font-size: 2rem; font-weight: 800; margin-bottom: 0.5rem; }
.page-subtitle { color: var(--text-muted); }

/* Search */
.search-box { position: relative; margin-bottom: 1.5rem; }
.search-icon { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); }
.search-input {
    width: 100%; padding: 1rem 1rem 1rem 3rem;
    border: 1.5px solid var(--border-color); border-radius: var(--radius-md);
    font-size: 0.95rem; font-family: inherit; background: white; transition: all 0.2s;
}
.search-input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 4px rgba(234, 88, 12, 0.1); }

/* Tabs */
.filter-tabs { display: flex; gap: 0.75rem; overflow-x: auto; padding-bottom: 1rem; margin-bottom: 2rem; scrollbar-width: none; }
.filter-tabs::-webkit-scrollbar { display: none; }
.tab-btn {
    padding: 0.6rem 1.25rem; border-radius: var(--radius-full); font-size: 0.85rem;
    font-weight: 600; border: 1px solid var(--border-color); background: white;
    color: var(--text-muted); cursor: pointer; white-space: nowrap; transition: all 0.2s;
}
.tab-btn:hover { background: var(--bg-main); color: var(--text-dark); }
.tab-btn.active { background: var(--primary); color: white; border-color: var(--primary); }

/* Order Cards */
.order-list { display: flex; flex-direction: column; gap: 1.5rem; }
.order-card {
    background: white; border-radius: var(--radius-lg); padding: 1.5rem;
    border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); transition: all 0.3s;
}
.order-card:hover { box-shadow: var(--shadow-lg); transform: translateY(-2px); border-color: var(--primary-light); }

.order-header {
    display: flex; justify-content: space-between; align-items: flex-start;
    margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px dashed var(--border-color);
}
.order-number { font-weight: 800; font-size: 1.15rem; }
.order-date   { font-size: 0.8rem; color: var(--text-muted); margin-top: 0.25rem; }

.status-badge {
    padding: 0.4rem 0.8rem; border-radius: var(--radius-full); font-size: 0.75rem;
    font-weight: 700; display: inline-flex; align-items: center; gap: 0.4rem;
}
.status-menunggu  { background: #EFF6FF; color: #1D4ED8; }
.status-diproses  { background: #FEF3C7; color: #B45309; }
.status-siap      { background: #ECFDF5; color: #047857; }
.status-selesai   { background: #F1F5F9; color: #475569; }
.status-dibatalkan{ background: #FEE2E2; color: #B91C1C; }

/* Progress */
.progress-labels { display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; margin-bottom: 0.5rem; }
.progress-bar-bg  { height: 6px; background: var(--bg-main); border-radius: 3px; overflow: hidden; }
.progress-bar-fill{ height: 100%; border-radius: 3px; transition: width 0.8s; }

/* Order items */
.order-items { display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.5rem; }
.order-item-row { display: flex; align-items: center; font-size: 0.95rem; }
.item-qty  { font-weight: 700; color: var(--primary); width: 35px; }
.item-name { color: var(--text-dark); font-weight: 500; }

/* Footer */
.order-footer { display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; border-top: 1px solid var(--border-color); }

/* ── Bukti Pesanan Modal ── */
.receipt-modal-overlay {
    position: fixed; inset: 0; background: rgba(15,23,42,0.5); backdrop-filter: blur(8px);
    z-index: 1000; display: flex; align-items: center; justify-content: center;
    padding: 1rem;
}
.receipt-card {
    background: white; border-radius: 1.5rem; width: 100%; max-width: 420px;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.2); overflow: hidden;
}
.receipt-header {
    background: var(--primary); color: white; padding: 1.75rem 2rem;
    text-align: center;
}
.receipt-qr-area {
    padding: 1.75rem 2rem;
    border-bottom: 2px dashed var(--border-color);
}
.receipt-qr-box {
    background: #F8FAFC; border: 2px solid var(--border-color);
    border-radius: 1rem; padding: 1.5rem; text-align: center; margin-bottom: 1.25rem;
}
.receipt-row {
    display: flex; justify-content: space-between; align-items: flex-start;
    padding: 0.625rem 0; border-bottom: 1px solid #F1F5F9; font-size: 0.875rem;
}
.receipt-row:last-child { border-bottom: none; }
.receipt-label { color: var(--text-muted); font-weight: 600; }
.receipt-value { font-weight: 700; color: var(--text-dark); text-align: right; max-width: 55%; }
.receipt-total-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 1rem 0; font-size: 1.1rem;
}
.receipt-actions {
    padding: 1.25rem 2rem; background: #F8FAFC;
    display: flex; gap: 0.75rem;
}

/* Empty State */
.empty-state { text-align: center; padding: 5rem 2rem; background: white; border-radius: var(--radius-lg); border: 1px dashed var(--border-color); }
</style>

<div class="history-container animate-slide-up" x-data="orderHistory()">

    <div style="margin-bottom: 2rem;">
        <a href="{{ route('menu') }}" class="btn btn-outline" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; font-size: 0.9rem;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            Kembali ke Menu
        </a>
    </div>

    <div style="margin-bottom: 2rem;">
        <h1 class="page-title">Riwayat Pesanan</h1>
        <p class="page-subtitle">Pantau status pesanan dan tampilkan bukti ke kasir.</p>
    </div>

    <!-- Search -->
    <div class="search-box delay-1">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="search-icon"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" class="search-input" x-model="searchQuery" placeholder="Cari nomor pesanan (Contoh: ORD-...)">
    </div>

    <!-- Filters -->
    <div class="filter-tabs delay-2">
        <button class="tab-btn" :class="{ 'active': filter === 'all' }"        @click="filter = 'all'">Semua</button>
        <button class="tab-btn" :class="{ 'active': filter === 'menunggu' }"   @click="filter = 'menunggu'">Menunggu</button>
        <button class="tab-btn" :class="{ 'active': filter === 'diproses' }"   @click="filter = 'diproses'">Dimasak</button>
        <button class="tab-btn" :class="{ 'active': filter === 'siap' }"       @click="filter = 'siap'">Siap Ambil</button>
        <button class="tab-btn" :class="{ 'active': filter === 'selesai' }"    @click="filter = 'selesai'">Selesai</button>
        <button class="tab-btn" :class="{ 'active': filter === 'dibatalkan' }" @click="filter = 'dibatalkan'">Dibatalkan</button>
    </div>

    <!-- Order List -->
    <div class="order-list">
        <template x-for="(order, index) in filteredOrders" :key="order.id">
            <div class="order-card" x-data="{ show: false }" x-init="setTimeout(() => show = true, index * 100)" x-show="show" x-transition>

                <div class="order-header">
                    <div>
                        <div class="order-number" x-text="order.order_number"></div>
                        <div class="order-date" x-text="formatDate(order.created_at)"></div>
                    </div>
                    <div class="status-badge" :class="'status-' + order.status">
                        <span x-html="getStatusIcon(order.status)"></span>
                        <span x-text="getStatusLabel(order.status)"></span>
                    </div>
                </div>

                <!-- Progress Bar untuk pesanan aktif -->
                <div x-show="['menunggu', 'diproses', 'siap'].includes(order.status)" style="margin-bottom:1.5rem;">
                    <div class="progress-labels">
                        <span :style="order.status === 'menunggu' ? 'color:var(--text-dark)' : ''">Diterima</span>
                        <span :style="order.status === 'diproses' ? 'color:var(--text-dark)' : ''">Dimasak</span>
                        <span :style="order.status === 'siap' ? 'color:var(--text-dark)' : ''">Siap Diambil</span>
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" :style="'width:' + getProgressPercent(order.status) + '%; background:' + getProgressColor(order.status)"></div>
                    </div>
                </div>

                <div class="order-items">
                    <template x-for="item in order.items.slice(0, 3)" :key="item.id">
                        <div class="order-item-row">
                            <span class="item-qty" x-text="item.quantity + 'x'"></span>
                            <span class="item-name" x-text="item.name"></span>
                        </div>
                    </template>
                    <div x-show="order.items.length > 3" style="font-size: 0.85rem; color: var(--text-muted); font-style: italic;">
                        + <span x-text="order.items.length - 3"></span> menu lainnya
                    </div>
                </div>

                <div class="order-footer">
                    <div>
                        <div style="font-size:0.7rem; color:var(--text-muted); font-weight:700; text-transform:uppercase; margin-bottom:0.2rem;">Tipe Pesanan</div>
                        <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-dark);" x-text="order.order_type === 'dine_in' ? 'Makan di Tempat' : 'Bungkus'"></div>
                    </div>
                    <div style="display:flex; align-items:center; gap:0.75rem;">
                        {{-- Tombol Lihat Bukti --}}
                        <button
                            @click="openReceipt(order)"
                            style="display:flex; align-items:center; gap:0.4rem; padding:0.5rem 0.875rem; background:var(--primary-light); color:var(--primary); border:none; border-radius:var(--radius-md); font-weight:700; font-size:0.8rem; cursor:pointer; transition:all 0.2s;"
                            onmouseover="this.style.background='var(--primary)'; this.style.color='white';"
                            onmouseout="this.style.background='var(--primary-light)'; this.style.color='var(--primary)';">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                            Bukti Pesanan
                        </button>
                        <div style="text-align: right;">
                            <div style="font-size:0.7rem; color:var(--text-muted); font-weight:700; text-transform:uppercase; margin-bottom:0.2rem;">Total</div>
                            <div style="font-size: 1.1rem; font-weight: 800; color: var(--primary);" x-text="'Rp ' + parseInt(order.total).toLocaleString('id-ID')"></div>
                        </div>
                    </div>
                </div>

            </div>
        </template>

        <!-- Empty State -->
        <div x-show="filteredOrders.length === 0" class="empty-state" style="display: none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--text-muted); margin: 0 auto 1.5rem;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem;">Tidak ada pesanan</h3>
            <p style="color: var(--text-muted);">Belum ada riwayat pesanan dengan filter ini.</p>
        </div>
    </div>

    {{-- ── Modal Bukti Pesanan ─────────────────────────────────────────────── --}}
    <div class="receipt-modal-overlay" x-show="receiptOrder !== null" x-transition style="display:none;" @click.self="receiptOrder = null">
        <div class="receipt-card" @click.stop x-show="receiptOrder !== null" x-transition>
            <template x-if="receiptOrder">
                <div>
                    {{-- Header --}}
                    <div class="receipt-header">
                        <div style="display:flex; align-items:center; justify-content:center; gap:0.75rem; margin-bottom:0.75rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/><path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"/></svg>
                            <span style="font-size:1.25rem; font-weight:900;">KantinVokasi</span>
                        </div>
                        <div style="font-size:0.8rem; opacity:0.85; margin-bottom:0.5rem;">BUKTI PESANAN</div>
                        <div style="font-size:1.5rem; font-weight:900; letter-spacing:0.05em;" x-text="receiptOrder.order_number"></div>
                    </div>

                    {{-- QR / Barcode visual --}}
                    <div class="receipt-qr-area">
                        <div class="receipt-qr-box">
                            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 0.75rem;"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><path d="M14 14h.01M18 14h.01M14 18h.01M18 18h.01M14 21h.01M21 14h.01M21 18h.01M21 21h.01"/></svg>
                            <div style="font-size:0.8rem; color:var(--text-muted); font-weight:600;">Tunjukkan ke kasir untuk verifikasi</div>
                        </div>

                        {{-- Detail pesanan --}}
                        <div class="receipt-row">
                            <span class="receipt-label">Pemesan</span>
                            <span class="receipt-value">{{ auth()->user()->name }}</span>
                        </div>
                        <div class="receipt-row">
                            <span class="receipt-label">Tanggal</span>
                            <span class="receipt-value" x-text="formatDateFull(receiptOrder.created_at)"></span>
                        </div>
                        <div class="receipt-row">
                            <span class="receipt-label">Tipe</span>
                            <span class="receipt-value" x-text="receiptOrder.order_type === 'dine_in' ? 'Makan di Tempat' : 'Bungkus'"></span>
                        </div>
                        <div class="receipt-row">
                            <span class="receipt-label">Pembayaran</span>
                            <span class="receipt-value" x-text="receiptOrder.payment_method === 'qris' ? 'QRIS' : 'Tunai'"></span>
                        </div>
                        <div class="receipt-row">
                            <span class="receipt-label">Status Bayar</span>
                            <span class="receipt-value" x-text="receiptOrder.payment_status === 'sudah_bayar' ? 'Lunas' : 'Belum Dibayar'" :style="receiptOrder.payment_status === 'sudah_bayar' ? 'color:#059669' : 'color:#DC2626'"></span>
                        </div>

                        {{-- Item list --}}
                        <div style="margin-top:1rem; padding-top:1rem; border-top:1px dashed var(--border-color);">
                            <div style="font-size:0.75rem; font-weight:800; color:var(--text-muted); text-transform:uppercase; margin-bottom:0.625rem;">Daftar Pesanan</div>
                            <template x-for="item in receiptOrder.items" :key="item.id">
                                <div style="display:flex; justify-content:space-between; font-size:0.875rem; margin-bottom:0.375rem;">
                                    <span style="color:var(--text-dark); font-weight:600;">
                                        <span style="color:var(--primary); font-weight:800;" x-text="item.quantity + 'x'"></span>
                                        <span x-text="' ' + item.name"></span>
                                    </span>
                                    <span style="font-weight:700; color:var(--text-muted);" x-text="'Rp ' + parseInt(item.subtotal).toLocaleString('id-ID')"></span>
                                </div>
                            </template>
                        </div>

                        <div class="receipt-total-row" style="border-top: 2px solid var(--border-color); margin-top:0.75rem; padding-top:0.75rem;">
                            <span style="font-weight:800; color:var(--text-dark);">TOTAL</span>
                            <span style="font-weight:900; color:var(--primary); font-size:1.25rem;" x-text="'Rp ' + parseInt(receiptOrder.total).toLocaleString('id-ID')"></span>
                        </div>
                    </div>

                    {{-- Tombol aksi --}}
                    <div class="receipt-actions">
                        <button @click="receiptOrder = null" style="flex:1; padding:0.875rem; border:1.5px solid var(--border-color); background:white; border-radius:0.875rem; font-weight:700; font-size:0.9rem; cursor:pointer; color:var(--text-dark);">
                            Tutup
                        </button>
                        <button onclick="window.print()" style="flex:1; padding:0.875rem; background:var(--primary); color:white; border:none; border-radius:0.875rem; font-weight:700; font-size:0.9rem; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:0.5rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                            Cetak
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

</div>

{{-- Realtime --}}
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    const pusher = new Pusher('{{ config("broadcasting.connections.reverb.key") }}', {
        wsHost: window.location.hostname,
        wsPort: {{ config("broadcasting.connections.reverb.options.port", 8080) }},
        forceTLS: false, disableStats: true, cluster: 'mt1'
    });
    const channel = pusher.subscribe('kitchen-orders');
    channel.bind('kitchen.status.updated', function() { location.reload(); });
</script>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('orderHistory', () => ({
        searchQuery: '',
        filter: 'all',
        allOrders: @json($orders),
        receiptOrder: null,

        get filteredOrders() {
            return this.allOrders.filter(order => {
                const matchSearch = order.order_number.toLowerCase().includes(this.searchQuery.toLowerCase());
                const matchFilter = this.filter === 'all' || order.status === this.filter;
                return matchSearch && matchFilter;
            });
        },

        openReceipt(order) { this.receiptOrder = order; },

        formatDate(dateStr) {
            return new Date(dateStr).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        },

        formatDateFull(dateStr) {
            return new Date(dateStr).toLocaleDateString('id-ID', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        },

        getStatusLabel(status) {
            return { menunggu: 'Menunggu Dapur', diproses: 'Sedang Dimasak', siap: 'Siap Diambil', selesai: 'Selesai', dibatalkan: 'Dibatalkan' }[status] || status;
        },

        getStatusIcon(status) {
            const icons = {
                menunggu:   '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
                diproses:   '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v2"/><path d="M12 18v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/></svg>',
                siap:       '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
                selesai:    '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
                dibatalkan: '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
            };
            return icons[status] || '';
        },

        getProgressPercent(status) {
            return { menunggu: 15, diproses: 50, siap: 100 }[status] || 0;
        },

        getProgressColor(status) {
            return { menunggu: '#3B82F6', diproses: '#F59E0B', siap: '#10B981' }[status] || '#E2E8F0';
        }
    }));
});
</script>

{{-- Print style: hanya tampilkan receipt card saat print --}}
<style>
@media print {
    body > * { display: none !important; }
    .receipt-modal-overlay, .receipt-card { display: block !important; position: static !important; background: white !important; box-shadow: none !important; }
}
</style>
@endsection