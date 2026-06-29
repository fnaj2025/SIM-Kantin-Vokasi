@extends('layouts.internal')
@section('title', 'Point of Sales')

@push('styles')
<style>
/* ── POS Layout ── */
.pos-grid {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 1.5rem;
    height: calc(100vh - 120px);
}
@media(max-width: 1024px) { .pos-grid { grid-template-columns: 1fr; height: auto; } }

/* Menu Panel */
.menu-panel { display: flex; flex-direction: column; gap: 1.25rem; overflow: hidden; }

.search-container {
    display: flex; gap: 1rem; align-items: center;
    background: white; padding: 0.5rem 1.25rem;
    border-radius: 1rem; border: 1.5px solid var(--border);
    box-shadow: var(--shadow-sm);
}
.search-input { flex: 1; border: none; padding: 0.5rem 0; font-size: 0.95rem; font-family: inherit; font-weight: 500; background: transparent; color: var(--text-dark); }
.search-input:focus { outline: none; }

.cat-tabs { display: flex; gap: 0.75rem; overflow-x: auto; padding-bottom: 0.5rem; scrollbar-width: none; }
.cat-tabs::-webkit-scrollbar { display: none; }
.cat-tab {
    padding: 0.5rem 1.1rem; border-radius: var(--radius-full); font-size: 0.8rem; font-weight: 700;
    cursor: pointer; border: 1.5px solid var(--border); background: white; color: var(--text-muted);
    transition: 0.15s; white-space: nowrap; display: flex; align-items: center; gap: 0.4rem;
}
.cat-tab.active { background: var(--primary); color: white; border-color: var(--primary); box-shadow: var(--shadow-orange); }
.cat-tab:not(.active):hover { border-color: var(--primary); color: var(--primary); }

/* Menu Grid */
.menu-grid {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 1rem; overflow-y: auto; padding-right: 4px; padding-bottom: 2rem;
}
.menu-grid::-webkit-scrollbar { width: 4px; }
.menu-grid::-webkit-scrollbar-thumb { background: var(--border); border-radius: 10px; }

.menu-card {
    background: white; border: 1.5px solid var(--border); border-radius: var(--radius-lg);
    padding: 1.125rem 1rem; cursor: pointer; transition: 0.2s; position: relative;
    display: flex; flex-direction: column; align-items: center; text-align: center;
}
.menu-card:hover { border-color: var(--primary); transform: translateY(-3px); box-shadow: 0 8px 16px -4px rgba(234,88,12,0.12); }
.menu-card.out-of-stock { opacity: 0.45; cursor: not-allowed; pointer-events: none; filter: grayscale(0.8); }

/* Category icon container */
.menu-icon-wrap {
    width: 56px; height: 56px; border-radius: var(--radius-md);
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 0.875rem; flex-shrink: 0;
}
.menu-name { font-weight: 800; font-size: 0.875rem; color: var(--text-dark); margin-bottom: 0.375rem; line-height: 1.3; }
.menu-price { font-weight: 900; font-size: 1rem; color: var(--primary); }

.stock-badge {
    position: absolute; top: 0.6rem; right: 0.6rem;
    padding: 0.2rem 0.5rem; border-radius: 0.5rem;
    font-size: 0.6rem; font-weight: 800; text-transform: uppercase;
}
.stock-low { background: var(--primary-light); color: var(--primary); }
.stock-empty { background: var(--danger-bg); color: var(--danger-text); }

/* Cart Panel */
.cart-panel {
    background: white; border: 1.5px solid var(--border); border-radius: 1.5rem;
    display: flex; flex-direction: column; overflow: hidden; box-shadow: var(--shadow-sm);
}
.cart-header {
    padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-light);
    background: #FAFAFA; display: flex; align-items: center; justify-content: space-between;
}
.cart-header h3 { font-size: 1rem; font-weight: 900; color: var(--text-dark); display: flex; align-items: center; gap: 0.6rem; }
.cart-badge { background: var(--primary); color: white; padding: 0.2rem 0.65rem; border-radius: 99px; font-size: 0.7rem; font-weight: 800; }

.cart-body { flex: 1; overflow-y: auto; padding: 1.125rem; display: flex; flex-direction: column; gap: 0.875rem; }
.cart-body::-webkit-scrollbar { width: 4px; }
.cart-body::-webkit-scrollbar-thumb { background: var(--border); border-radius: 10px; }

.cart-item {
    display: flex; gap: 0.75rem; align-items: center; padding: 0.875rem 1rem;
    background: var(--bg-subtle); border-radius: var(--radius-md);
    border: 1px solid var(--border-light);
}
.cart-item-info { flex: 1; min-width: 0; }
.cart-item-name { font-weight: 700; font-size: 0.875rem; color: var(--text-dark); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.cart-item-sub { font-size: 0.8rem; color: var(--primary); font-weight: 700; margin-top: 2px; }

.qty-ctrl { display: flex; align-items: center; gap: 0.4rem; }
.qty-btn {
    width: 26px; height: 26px; border-radius: 50%; border: 1.5px solid var(--border);
    background: white; font-weight: 800; cursor: pointer; display: flex;
    align-items: center; justify-content: center; font-size: 1rem; color: var(--text-dark); transition: 0.15s;
}
.qty-btn:hover { background: var(--primary); color: white; border-color: var(--primary); }
.qty-val { font-weight: 800; font-size: 0.9rem; width: 22px; text-align: center; color: var(--text-dark); }

.cart-empty { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 3rem 1.5rem; color: var(--text-faint); }

/* Cart Footer */
.cart-footer { padding: 1.25rem; border-top: 1px solid var(--border-light); display: flex; flex-direction: column; gap: 1rem; }

.total-box {
    display: flex; justify-content: space-between; align-items: center;
    background: var(--bg-subtle); padding: 1rem 1.25rem; border-radius: var(--radius-md);
    border: 1.5px solid var(--border);
}
.total-label { font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.04em; }
.total-value { font-size: 1.25rem; font-weight: 900; color: var(--text-dark); }

.pos-label { display: block; font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.4rem; }
.pos-input-p {
    width: 100%; padding: 0.625rem 0.875rem; border: 1.5px solid var(--border);
    border-radius: var(--radius-md); font-size: 0.9rem; font-family: inherit;
    color: var(--text-dark); background: white; transition: 0.15s;
}
.pos-input-p:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-light); }

.toggle-group { display: flex; border: 1.5px solid var(--border); border-radius: var(--radius-md); overflow: hidden; }
.toggle-btn {
    flex: 1; padding: 0.55rem 0; border: none; background: white; color: var(--text-muted);
    font-size: 0.75rem; font-weight: 800; cursor: pointer; transition: 0.15s; font-family: inherit;
}
.toggle-btn.active { background: var(--primary); color: white; }

.pay-btn {
    width: 100%; padding: 1rem; border: none; background: var(--primary); color: white;
    border-radius: var(--radius-md); font-weight: 900; font-size: 0.95rem; cursor: pointer;
    transition: 0.2s; box-shadow: 0 4px 12px -2px rgba(234,88,12,0.3); font-family: inherit;
}
.pay-btn:hover:not(:disabled) { background: var(--primary-hover); transform: translateY(-1px); }
.pay-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

/* Receipt Modal */
.receipt-overlay {
    position: fixed; inset: 0; background: rgba(15,23,42,0.5); backdrop-filter: blur(6px);
    z-index: 300; display: flex; align-items: center; justify-content: center; padding: 1rem;
}
.receipt-card {
    background: white; border-radius: 1.5rem; width: 100%; max-width: 420px;
    max-height: 90vh; overflow-y: auto;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
}
.receipt-header { padding: 1.5rem 1.5rem 0; text-align: center; }
.receipt-success-icon {
    width: 64px; height: 64px; background: var(--success-bg); border-radius: 50%;
    display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;
}
.receipt-title { font-size: 1.25rem; font-weight: 900; color: var(--text-dark); margin-bottom: 0.25rem; }
.receipt-sub { font-size: 0.875rem; color: var(--text-muted); font-weight: 500; }

.receipt-body { padding: 1.25rem 1.5rem; }
.receipt-store { text-align: center; padding-bottom: 1rem; border-bottom: 1px dashed var(--border); margin-bottom: 1rem; }
.receipt-store-name { font-size: 1rem; font-weight: 900; color: var(--text-dark); }
.receipt-store-sub { font-size: 0.75rem; color: var(--text-muted); }

.receipt-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 0.625rem; margin-bottom: 1rem; }
.receipt-meta-item { background: var(--bg-subtle); border-radius: var(--radius-sm); padding: 0.625rem 0.75rem; }
.receipt-meta-label { font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-faint); margin-bottom: 2px; }
.receipt-meta-value { font-size: 0.8rem; font-weight: 700; color: var(--text-dark); }

.receipt-items { border-top: 1px dashed var(--border); padding-top: 1rem; margin-bottom: 1rem; }
.receipt-item-row { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.5rem; font-size: 0.875rem; }
.receipt-item-name { font-weight: 600; color: var(--text-body); flex: 1; }
.receipt-item-qty { color: var(--text-muted); font-size: 0.8rem; margin: 0 0.5rem; }
.receipt-item-price { font-weight: 700; color: var(--text-dark); white-space: nowrap; }

.receipt-total-row {
    display: flex; justify-content: space-between; align-items: center;
    border-top: 2px solid var(--text-dark); padding-top: 0.75rem; margin-top: 0.5rem;
}
.receipt-total-label { font-size: 0.875rem; font-weight: 800; color: var(--text-dark); }
.receipt-total-value { font-size: 1.25rem; font-weight: 900; color: var(--primary); }

.receipt-footer { padding: 1rem 1.5rem 1.5rem; display: flex; flex-direction: column; gap: 0.75rem; border-top: 1px solid var(--border-light); }
.receipt-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }

@media print {
    /* Sembunyikan semua kecuali receipt card */
    body > * { display: none !important; }
    .receipt-overlay { display: flex !important; position: static !important; background: white !important; }
    .receipt-card { box-shadow: none !important; border-radius: 0 !important; max-height: none !important; overflow: visible !important; }
    .receipt-footer { display: none !important; }
}

/* Mencegah flash sebelum Alpine init */
[x-cloak] { display: none !important; }

/* ── Menu card: foto dari DB ── */
.menu-img-wrap {
    width: 100%; height: 88px;
    border-radius: var(--radius-md);
    overflow: hidden; margin-bottom: 0.875rem;
    background: var(--bg-subtle); flex-shrink: 0;
}
.menu-img-pos { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease; }
.menu-card:hover .menu-img-pos { transform: scale(1.06); }

/* ── Toast notifikasi print ── */
.print-toast {
    position: fixed; bottom: 2rem; left: 50%;
    transform: translateX(-50%) translateY(16px);
    background: var(--text-dark); color: white;
    padding: 0.875rem 1.5rem; border-radius: var(--radius-lg);
    font-size: 0.875rem; font-weight: 700;
    display: flex; align-items: center; gap: 0.625rem;
    box-shadow: 0 8px 24px rgba(0,0,0,0.2);
    z-index: 9999; opacity: 0;
    transition: all 0.3s cubic-bezier(0.16,1,0.3,1);
    pointer-events: none; white-space: nowrap;
}
.print-toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
</style>
@endpush

@section('content')

<div x-data="posApp()" style="height: calc(100vh - var(--topbar-h) - 3.5rem);">

    {{-- Page Header --}}
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
        <div>
            <h1 class="page-title">Point of Sales</h1>
            <p class="page-sub">Proses pesanan kasir secara langsung</p>
        </div>
        <div style="display:flex; align-items:center; gap:0.75rem; font-size:0.8rem; color:var(--text-muted); font-weight:600;">
            <span style="width:7px; height:7px; background:var(--success); border-radius:50%; display:inline-block;"></span>
            Kasir Aktif
        </div>
    </div>

    <div class="pos-grid">

        {{-- LEFT: Menu Panel --}}
        <div class="menu-panel">

            {{-- Search --}}
            <div class="search-container">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color:var(--text-faint);flex-shrink:0;"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <input type="text" x-model="search" @input="filterItems()" class="search-input" placeholder="Cari nama menu...">
                <template x-if="search">
                    <button @click="search=''; filterItems()" style="background:none; border:none; color:var(--text-faint); cursor:pointer; padding:0.25rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </template>
            </div>

            {{-- Category Tabs --}}
            <div class="cat-tabs">
                <button class="cat-tab" :class="{ active: !activeCat }" @click="activeCat=null; filterItems()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
                    Semua
                </button>
                @foreach($categories as $cat)
                <button class="cat-tab" :class="{ active: activeCat === {{ $cat->id }} }" @click="activeCat={{ $cat->id }}; filterItems()">
                    {{ $cat->name }}
                </button>
                @endforeach
            </div>

            {{-- Menu Grid --}}
            <div class="menu-grid">
                <template x-for="item in filteredItems" :key="item.id">
                    <div class="menu-card" :class="{ 'out-of-stock': item.stock <= 0 }" @click="addToCart(item)">

                        {{-- Stock badge --}}
                        <div x-show="item.stock > 0 && item.stock <= 5" class="stock-badge stock-low">
                            Sisa <span x-text="item.stock"></span>
                        </div>
                        <div x-show="item.stock <= 0" class="stock-badge stock-empty">Habis</div>

                        {{-- Foto jika ada, icon SVG jika tidak ada foto --}}
                        <template x-if="item.image">
                            <div class="menu-img-wrap">
                                <img :src="item.image" :alt="item.name" class="menu-img-pos"
                                     onerror="this.parentElement.style.display='none'; this.parentElement.nextElementSibling.style.display='flex';">
                            </div>
                        </template>
                        <template x-if="!item.image">
                            <div class="menu-icon-wrap" :style="`background: ${item.iconBg}`">
                                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" :stroke="item.iconColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path :d="item.iconPath"></path>
                                </svg>
                            </div>
                        </template>

                        <div class="menu-name" x-text="item.name"></div>
                        <div class="menu-price" x-text="'Rp ' + item.price.toLocaleString('id-ID')"></div>
                    </div>
                </template>

                <template x-if="filteredItems.length === 0">
                    <div style="grid-column:1/-1; text-align:center; padding:4rem 1rem; color:var(--text-faint);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 1rem;display:block;"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                        <div style="font-weight:700;">Tidak ada menu</div>
                    </div>
                </template>
            </div>
        </div>

        {{-- RIGHT: Cart Panel --}}
        <div class="cart-panel">
            <div class="cart-header">
                <h3>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color:var(--primary)"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                    Pesanan
                    <span class="cart-badge" x-text="cart.length"></span>
                </h3>
                <button x-show="cart.length > 0" @click="cart = []" style="background:none; border:none; color:var(--text-faint); cursor:pointer; font-size:0.75rem; font-weight:700; display:flex; align-items:center; gap:0.3rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                    Kosongkan
                </button>
            </div>

            <div class="cart-body">
                <template x-if="cart.length === 0">
                    <div class="cart-empty">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom:1rem;"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                        <div style="font-weight:700; font-size:0.875rem;">Pilih menu di sebelah kiri</div>
                    </div>
                </template>

                <template x-for="(item, idx) in cart" :key="item.id">
                    <div class="cart-item">
                        <div class="cart-item-info">
                            <div class="cart-item-name" x-text="item.name"></div>
                            <div class="cart-item-sub" x-text="'Rp ' + (item.price * item.qty).toLocaleString('id-ID')"></div>
                        </div>
                        <div class="qty-ctrl">
                            <button class="qty-btn" @click="decrease(idx)">−</button>
                            <span class="qty-val" x-text="item.qty"></span>
                            <button class="qty-btn" @click="increase(idx)">+</button>
                        </div>
                    </div>
                </template>
            </div>

            <div class="cart-footer">
                <div class="total-box">
                    <span class="total-label">Total</span>
                    <span class="total-value" x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
                </div>

                <div>
                    <label class="pos-label">Nama / Meja</label>
                    <input type="text" x-model="customerName" class="pos-input-p" placeholder="Cth: Meja 5 / Budi">
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem;">
                    <div>
                        <label class="pos-label">Metode Bayar</label>
                        <div class="toggle-group">
                            <button class="toggle-btn" :class="{ active: payMethod === 'tunai' }" @click="payMethod = 'tunai'">Tunai</button>
                            <button class="toggle-btn" :class="{ active: payMethod === 'qris' }" @click="payMethod = 'qris'">QRIS</button>
                        </div>
                    </div>
                    <div>
                        <label class="pos-label">Tipe Pesanan</label>
                        <div class="toggle-group">
                            <button class="toggle-btn" :class="{ active: orderType === 'dine_in' }" @click="orderType = 'dine_in'">Makan</button>
                            <button class="toggle-btn" :class="{ active: orderType === 'takeaway' }" @click="orderType = 'takeaway'">Bawa</button>
                        </div>
                    </div>
                </div>

                <button class="pay-btn" :disabled="cart.length === 0 || !customerName.trim() || isLoading" @click="checkout()">
                    <template x-if="!isLoading">
                        <span style="display:flex;align-items:center;justify-content:center;gap:0.5rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                            Proses Pesanan
                        </span>
                    </template>
                    <template x-if="isLoading">
                        <span>Memproses...</span>
                    </template>
                </button>
            </div>
        </div>
    </div>

    {{-- ── RECEIPT MODAL ── --}}
    <div class="receipt-overlay" x-show="showReceipt" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="receipt-card" id="printArea">

            <div class="receipt-header">
                <div class="receipt-success-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <div class="receipt-title">Transaksi Berhasil</div>
                <div class="receipt-sub" x-text="'No. Pesanan: ' + lastOrder.order_number"></div>
            </div>

            <div class="receipt-body">
                {{-- Store Info --}}
                <div class="receipt-store">
                    <div class="receipt-store-name">KantinVokasi</div>
                    <div class="receipt-store-sub">Internal POS System</div>
                    <div class="receipt-store-sub" x-text="lastOrder.date"></div>
                </div>

                {{-- Meta Info --}}
                <div class="receipt-meta">
                    <div class="receipt-meta-item">
                        <div class="receipt-meta-label">Nama / Meja</div>
                        <div class="receipt-meta-value" x-text="lastOrder.customer_name"></div>
                    </div>
                    <div class="receipt-meta-item">
                        <div class="receipt-meta-label">Kasir</div>
                        <div class="receipt-meta-value">{{ auth()->user()->name }}</div>
                    </div>
                    <div class="receipt-meta-item">
                        <div class="receipt-meta-label">Pembayaran</div>
                        <div class="receipt-meta-value" x-text="lastOrder.payment_method === 'tunai' ? 'Tunai' : 'QRIS'"></div>
                    </div>
                    <div class="receipt-meta-item">
                        <div class="receipt-meta-label">Tipe</div>
                        <div class="receipt-meta-value" x-text="lastOrder.order_type === 'dine_in' ? 'Makan di Tempat' : 'Bawa Pulang'"></div>
                    </div>
                </div>

                {{-- Items --}}
                <div class="receipt-items">
                    <template x-for="item in lastOrder.items" :key="item.id">
                        <div class="receipt-item-row">
                            <span class="receipt-item-name" x-text="item.name"></span>
                            <span class="receipt-item-qty" x-text="'x' + item.qty"></span>
                            <span class="receipt-item-price" x-text="'Rp ' + (item.price * item.qty).toLocaleString('id-ID')"></span>
                        </div>
                    </template>
                    <div class="receipt-total-row">
                        <span class="receipt-total-label">Total Bayar</span>
                        <span class="receipt-total-value" x-text="'Rp ' + lastOrder.total.toLocaleString('id-ID')"></span>
                    </div>
                </div>

                <div style="text-align:center; font-size:0.75rem; color:var(--text-faint); padding-top:0.5rem;">
                    Pesanan dikirim ke dapur. Terima kasih!
                </div>
            </div>

            <div class="receipt-footer">
                <button @click="printReceipt()"
                        style="width:100%; padding:0.875rem; border:1.5px solid var(--border); background:white; border-radius:var(--radius-md); font-weight:700; font-size:0.875rem; cursor:pointer; color:var(--text-body); font-family:inherit; transition:0.15s; display:flex; align-items:center; justify-content:center; gap:0.5rem;"
                        onmouseover="this.style.borderColor='var(--primary)';this.style.color='var(--primary)'"
                        onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text-body)'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                    Cetak Struk ke Printer
                </button>
                <button @click="resetAll()"
                        style="width:100%; padding:0.875rem; border:none; background:var(--primary); color:white; border-radius:var(--radius-md); font-weight:900; font-size:0.95rem; cursor:pointer; font-family:inherit; transition:0.2s;"
                        onmouseover="this.style.background='var(--primary-hover)'"
                        onmouseout="this.style.background='var(--primary)'">
                    Pesanan Baru
                </button>
            </div>
        </div>
    </div>

{{-- Toast notifikasi print --}}
<div id="printToast" class="print-toast">
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
    Struk sedang dikirim ke printer...
</div>

</div>
@endsection

@push('scripts')
<script>
@php
$menuData = $menuItems->map(function($m) {
    $cat = strtolower($m->category->name ?? '');

    // Icon SVG per kategori — fallback jika tidak ada foto
    $icons = [
        'makanan'  => ['path'=>'M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2M7 2v20M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3zm0 0v7', 'bg'=>'#FFEDD5', 'color'=>'#EA580C'],
        'minuman'  => ['path'=>'M8 22h8M7 10h10l-1 7a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L6 10zM12 10V4M8 4h8',                          'bg'=>'#DBEAFE', 'color'=>'#2563EB'],
        'snack'    => ['path'=>'M12 2c-5.52 0-10 4.48-10 10s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2M8 14s1.5 2 4 2 4-2 4-2', 'bg'=>'#FEF3C7', 'color'=>'#D97706'],
        'cemilan'  => ['path'=>'M12 2c-5.52 0-10 4.48-10 10s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2M8 14s1.5 2 4 2 4-2 4-2', 'bg'=>'#FEF3C7', 'color'=>'#D97706'],
        'dessert'  => ['path'=>'M12 22c4.97 0 9-2.69 9-6V8c0-3.31-4.03-6-9-6S3 4.69 3 8v8c0 3.31 4.03 6 9 6z',                 'bg'=>'#FCE7F3', 'color'=>'#DB2777'],
        'paket'    => ['path'=>'M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z',                                                'bg'=>'#D1FAE5', 'color'=>'#059669'],
        'nasi'     => ['path'=>'M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2M7 2v20M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3zm0 0v7', 'bg'=>'#FFEDD5', 'color'=>'#EA580C'],
        'mie'      => ['path'=>'M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2M7 2v20M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3zm0 0v7', 'bg'=>'#FEF9C3', 'color'=>'#CA8A04'],
        'jus'      => ['path'=>'M8 22h8M7 10h10l-1 7a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L6 10zM12 10V4M8 4h8',                          'bg'=>'#DCFCE7', 'color'=>'#16A34A'],
        'kopi'     => ['path'=>'M17 8h1a4 4 0 0 1 0 8h-1M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V8zM6 2v3M10 2v3M14 2v3',       'bg'=>'#FEF3C7', 'color'=>'#92400E'],
        'es'       => ['path'=>'M8 22h8M7 10h10l-1 7a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L6 10zM12 10V4M8 4h8',                          'bg'=>'#DBEAFE', 'color'=>'#1D4ED8'],
    ];

    // Match kategori — cari keyword yang paling relevan
    $matched = 'makanan'; // default
    foreach ($icons as $key => $_) {
        if (str_contains($cat, $key)) {
            $matched = $key;
            break;
        }
    }

    return [
        'id'          => $m->id,
        'name'        => $m->name,
        'price'       => (float) $m->price,
        'stock'       => (int) ($m->stock ?? 99),
        'category_id' => $m->category_id,
        'image'       => $m->image ?: null,   // URL foto dari DB — sama persis dengan customer
        'iconPath'    => $icons[$matched]['path'],
        'iconBg'      => $icons[$matched]['bg'],
        'iconColor'   => $icons[$matched]['color'],
    ];
})->values();
@endphp

const allMenuItems = {!! json_encode($menuData) !!};

function posApp() {
    return {
        search: '',
        activeCat: null,
        allItems: allMenuItems,
        filteredItems: allMenuItems,

        cart: [],
        customerName: '',
        payMethod: 'tunai',
        orderType: 'dine_in',
        isLoading: false,
        showReceipt: false,
        lastOrder: {},

        get total() {
            return this.cart.reduce((s, i) => s + i.price * i.qty, 0);
        },

        filterItems() {
            this.filteredItems = this.allItems.filter(item => {
                const matchSearch = !this.search || item.name.toLowerCase().includes(this.search.toLowerCase());
                const matchCat    = !this.activeCat  || item.category_id === this.activeCat;
                return matchSearch && matchCat;
            });
        },

        addToCart(item) {
            if (item.stock <= 0) return;
            const existing = this.cart.find(c => c.id === item.id);
            if (existing) {
                existing.qty++;
            } else {
                this.cart.push({ ...item, qty: 1 });
            }
        },

        increase(idx) { this.cart[idx].qty++; },
        decrease(idx) {
            if (this.cart[idx].qty > 1) this.cart[idx].qty--;
            else this.cart.splice(idx, 1);
        },

        async checkout() {
            if (!this.customerName.trim() || this.cart.length === 0) return;
            this.isLoading = true;

            // Snapshot cart sebelum apapun berubah
            const cartSnapshot = this.cart.map(i => ({
                id: i.id, name: i.name, price: i.price, qty: i.qty
            }));
            const totalSnapshot    = this.total;
            const nameSnapshot     = this.customerName;
            const methodSnapshot   = this.payMethod;
            const typeSnapshot     = this.orderType;

            const payload = {
                customer_name:  nameSnapshot,
                payment_method: methodSnapshot,
                order_type:     typeSnapshot,
                items: cartSnapshot.map(i => ({ id: i.id, quantity: i.qty, price: i.price })),
            };

            try {
                const res  = await fetch('{{ route("internal.pos.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();

                if (data.success) {
                    const now = new Date();
                    const dateStr = now.toLocaleDateString('id-ID', {
                        weekday: 'long', year: 'numeric', month: 'long',
                        day: 'numeric', hour: '2-digit', minute: '2-digit'
                    });

                    // Isi lastOrder SEBELUM reset cart
                    this.lastOrder = {
                        order_number:   data.order_number,
                        total:          totalSnapshot,
                        customer_name:  nameSnapshot,
                        payment_method: methodSnapshot,
                        order_type:     typeSnapshot,
                        date:           dateStr,
                        items:          cartSnapshot,
                    };

                    // Reset cart setelah data tersimpan
                    this.cart         = [];
                    this.customerName = '';
                    this.payMethod    = 'tunai';
                    this.orderType    = 'dine_in';

                    // Tampilkan struk
                    this.showReceipt = true;

                } else {
                    alert('Gagal memproses: ' + (data.message || 'Terjadi kesalahan.'));
                }
            } catch (e) {
                alert('Koneksi bermasalah: ' + e.message);
            } finally {
                this.isLoading = false;
            }
        },

        printReceipt() {
            // Tampilkan toast notifikasi
            const toast = document.getElementById('printToast');
            toast.classList.add('show');

            // Beri jeda singkat lalu cetak — supaya toast sempat terlihat
            setTimeout(() => {
                window.print();
                // Sembunyikan toast setelah dialog print dibuka
                setTimeout(() => toast.classList.remove('show'), 2500);
            }, 300);
        },

        resetAll() {
            this.showReceipt = false;
            this.lastOrder   = {};
        }
    };
}
</script>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
const pusher = new Pusher('{{ config("broadcasting.connections.reverb.key") }}', {
    wsHost: window.location.hostname,
    wsPort: {{ config("broadcasting.connections.reverb.options.port", 8080) }},
    forceTLS: false, disableStats: true, cluster: 'mt1'
});
pusher.subscribe('pos-updates').bind('kitchen.status.updated', () => location.reload());
</script>
@endpush