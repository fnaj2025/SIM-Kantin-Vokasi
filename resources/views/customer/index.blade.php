@extends('layouts.customer')
@section('title', 'Katalog Menu')

@section('content')
<div style="margin: 2rem 0; text-align: left;" class="animate-slide-up">
    <a href="{{ route('home') }}" class="btn btn-outline" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; font-size: 0.9rem;">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        Kembali ke Beranda
    </a>
</div>

<div style="text-align: center; margin-bottom: 3rem;" class="animate-slide-up">
    <h1 style="font-size: 3rem; margin-bottom: 1rem;">Eksplorasi Menu Kami</h1>
    <p style="color: var(--text-muted); font-size: 1.125rem;">Pilih makanan dan minuman favoritmu, fresh dari dapur Kantin Vokasi.</p>
</div>

<style>
.category-wrapper {
    display: flex; gap: 1rem; overflow-x: auto;
    padding-bottom: 1.5rem; margin-bottom: 2rem; scrollbar-width: none;
}
.category-wrapper::-webkit-scrollbar { display: none; }

.category-btn {
    flex-shrink: 0; padding: 0.75rem 1.75rem; border-radius: var(--radius-full);
    font-weight: 600; cursor: pointer; transition: all 0.3s ease;
    border: 2px solid var(--border-color); background: white; color: var(--text-muted);
    display: flex; align-items: center; gap: 0.5rem;
}
.category-btn:hover { background: var(--bg-main); border-color: var(--primary-light); color: var(--primary); }
.category-btn.active {
    background: var(--primary); color: white; border-color: var(--primary);
    box-shadow: var(--shadow-orange); transform: translateY(-2px);
}

.empty-menu-state {
    grid-column: 1 / -1; text-align: center; padding: 6rem 2rem;
    background: white; border-radius: var(--radius-lg);
    border: 1px dashed var(--border-color); box-shadow: var(--shadow-sm);
}
</style>

<div x-data="menuCatalog()">

    {{-- ── Category Filter ── --}}
    <div class="category-wrapper animate-slide-up delay-1">
        <button class="category-btn" :class="{ 'active': activeCategory === 'all' }" @click="setCategory('all')">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"></path><path d="M7 2v20"></path><path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"></path></svg>
            Semua Menu
        </button>
        <template x-for="cat in categories" :key="cat.id">
            <button class="category-btn"
                    :class="{ 'active': activeCategory === cat.id }"
                    @click="setCategory(cat.id)"
                    x-text="cat.name">
            </button>
        </template>
    </div>

    @if(session('success'))
    <div style="background: var(--success); color: white; padding: 1rem 1.5rem; border-radius: var(--radius-md); margin-bottom: 2rem; display: flex; align-items: center; gap: 0.5rem; font-weight: 600;" class="animate-slide-up">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- ── Menu Grid ── --}}
    <div class="menu-grid">
        <template x-for="(item, index) in filteredItems" :key="item.id">
            <div class="menu-card"
                 x-data="{ show: false }"
                 x-init="setTimeout(() => show = true, index * 50)"
                 x-show="show" x-transition>

                <div class="menu-img-container">
                    {{-- ── FIX: gunakan item.image dari DB, fallback ke placeholder ── --}}
                    <img :src="item.image || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&q=80'"
                         :alt="item.name"
                         class="menu-img"
                         loading="lazy">
                    <div class="menu-badge" x-text="item.category ? item.category.name : 'Umum'"></div>
                </div>

                <div class="menu-info">
                    <h3 class="menu-title" x-text="item.name"></h3>
                    <p class="menu-desc" x-text="item.description ? item.description.substring(0, 60) + (item.description.length > 60 ? '...' : '') : 'Tidak ada deskripsi'"></p>
                    <div class="menu-footer">
                        <div class="menu-price" x-text="'Rp ' + parseInt(item.price).toLocaleString('id-ID')"></div>
                        <button @click="openModal(item)" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.85rem;">
                            Pesan
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <div x-show="filteredItems.length === 0" class="empty-menu-state" style="display: none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--text-muted); margin: 0 auto 1.5rem;"><path d="m2 2 20 20"></path><path d="M8.35 2.69A10 10 0 0 1 21.3 15.65"></path><path d="M19.08 19.08A10 10 0 1 1 4.92 4.92"></path></svg>
            <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Menu Tidak Ditemukan</h3>
            <p style="color: var(--text-muted); margin-bottom: 2rem;">Belum ada menu pada kategori ini.</p>
            <button @click="setCategory('all')" class="btn btn-primary">Lihat Semua Menu</button>
        </div>
    </div>

    {{-- ── Modal Pesan ── --}}
    <div class="modal-overlay" :class="{'active': selectedItem !== null}" @click="closeModal()">
        <div class="modal-card" @click.stop x-show="selectedItem !== null" x-transition>
            <template x-if="selectedItem">
                <div>
                    {{-- Gambar menu dari DB --}}
                    <div style="position: relative; height: 200px; background: var(--bg-main); overflow: hidden; border-radius: 1.5rem 1.5rem 0 0;">
                        <img :src="selectedItem.image || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&q=80'"
                             :alt="selectedItem.name"
                             style="width:100%; height:100%; object-fit:cover; border-radius:1.5rem 1.5rem 0 0;">
                        <button @click="closeModal()" style="position:absolute; top:1rem; right:1rem; background:rgba(255,255,255,0.9); border:none; width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; cursor:pointer; box-shadow:var(--shadow-sm);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>
                        </button>
                    </div>

                    <div style="padding: 2rem;">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1rem;">
                            <div>
                                <h3 style="font-size:1.5rem; margin-bottom:0.25rem;" x-text="selectedItem.name"></h3>
                                <div style="color:var(--primary); font-weight:800; font-size:1.25rem;" x-text="'Rp ' + parseInt(selectedItem.price).toLocaleString('id-ID')"></div>
                            </div>
                        </div>

                        <p style="color:var(--text-muted); font-size:0.95rem; margin-bottom:2rem; line-height:1.5;" x-text="selectedItem.description || 'Tidak ada deskripsi.'"></p>

                        {{-- ── FORM FIX ──
                             Gunakan hidden input untuk quantity yang dikirim ke server.
                             Input display (readonly) hanya untuk tampilan — TIDAK dikirim (tidak punya name).
                             Ini mencegah bug di mana x-model tidak sync ke value DOM saat submit.
                        --}}
                        <form action="{{ route('cart.add') }}" method="POST" @submit="syncQty">
                            @csrf
                            <input type="hidden" name="menu_item_id" :value="selectedItem.id">
                            {{-- hidden input ini yang dikirim ke server, nilainya di-set oleh Alpine --}}
                            <input type="hidden" name="quantity" :value="quantity" id="hiddenQty">

                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem; padding:1rem; background:var(--bg-main); border-radius:var(--radius-md); border:1px solid var(--border-color);">
                                <span style="font-weight:600;">Jumlah Pesanan</span>
                                <div class="qty-control" style="background:white;">
                                    {{-- Tombol - tidak punya name, hanya update state Alpine --}}
                                    <button type="button" class="qty-btn" @click="if(quantity > 1) quantity--">-</button>
                                    {{-- Display only — tidak punya name agar tidak dikirim double --}}
                                    <span class="qty-input" style="display:flex; align-items:center; justify-content:center; min-width:40px; font-weight:700; font-size:1rem;" x-text="quantity"></span>
                                    <button type="button" class="qty-btn" @click="quantity++">+</button>
                                </div>
                            </div>

                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                                <span style="color:var(--text-muted);">Subtotal</span>
                                <span style="font-size:1.25rem; font-weight:800; color:var(--text-dark);"
                                      x-text="'Rp ' + (selectedItem.price * quantity).toLocaleString('id-ID')"></span>
                            </div>

                            <button type="submit" class="btn btn-primary" style="width:100%; padding:1rem;">
                                Tambahkan ke Keranjang
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </template>
        </div>
    </div>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('menuCatalog', () => ({
        activeCategory: 'all',
        categories: @json($categories),
        allItems: @json($menuItems),
        selectedItem: null,
        quantity: 1,

        get filteredItems() {
            if (this.activeCategory === 'all') return this.allItems;
            return this.allItems.filter(item => item.category_id === this.activeCategory);
        },

        setCategory(id) { this.activeCategory = id; },

        openModal(item) {
            this.selectedItem = item;
            this.quantity = 1;
            document.body.style.overflow = 'hidden';
        },

        closeModal() {
            this.selectedItem = null;
            document.body.style.overflow = 'auto';
        },

        // Pastikan hidden input quantity ter-update sebelum form submit
        syncQty(event) {
            const hiddenInput = event.target.querySelector('#hiddenQty');
            if (hiddenInput) hiddenInput.value = this.quantity;
        }
    }));
});
</script>
@endsection