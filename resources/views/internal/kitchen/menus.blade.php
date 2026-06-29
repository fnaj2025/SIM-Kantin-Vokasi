@extends('layouts.internal')
@section('title', 'Manajemen Menu')

@section('content')
<style>
/* ── Menu Management — consistent dengan design system v2 ── */
.menu-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
}

.menu-card {
    background: var(--bg-card);
    border-radius: var(--radius-lg);
    padding: 1.25rem;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    transition: all 0.2s ease;
}

.menu-card:hover {
    box-shadow: var(--shadow-md);
    border-color: var(--primary-light);
    transform: translateY(-2px);
}

.menu-img {
    width: 100%;
    height: 160px;
    object-fit: cover;
    border-radius: var(--radius-md);
    margin-bottom: 1rem;
    background: var(--bg-subtle);
}

.menu-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.5rem;
}

.menu-title   { font-weight: 800; font-size: 1rem; color: var(--text-dark); }
.menu-category {
    font-size: 0.6875rem;
    background: var(--info-bg);
    color: var(--info-text);
    padding: 0.2rem 0.5rem;
    border-radius: var(--radius-sm);
    font-weight: 700;
    white-space: nowrap;
}

.menu-desc {
    font-size: 0.8125rem;
    color: var(--text-muted);
    margin-bottom: 0.875rem;
    flex: 1;
    line-height: 1.5;
}

.menu-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
    padding-top: 0.875rem;
    border-top: 1px solid var(--border-light);
}

.menu-price   { font-weight: 900; color: var(--primary); font-size: 1rem; }
.menu-actions { display: flex; gap: 0.4rem; }

/* Status pill */
.status-badge {
    padding: 0.25rem 0.65rem;
    border-radius: var(--radius-full);
    font-size: 0.6875rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
}

.status-active   { background: var(--success-bg); color: var(--success-text); }
.status-inactive { background: var(--danger-bg);  color: var(--danger-text); }
</style>

<div x-data="menuManager()">

    {{-- Page Header --}}
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.75rem; flex-wrap:wrap; gap:1rem;">
        <div>
            <h1 class="page-title">Manajemen Menu</h1>
            <p class="page-sub">Atur ketersediaan dan detail menu kantin</p>
        </div>
        <button @click="openModal('add')" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Menu
        </button>
    </div>

    {{-- Menu Grid --}}
    <div class="menu-grid">
        @foreach($menus as $menu)
        <div class="menu-card">
            <img
                src="{{ $menu->image ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&q=80' }}"
                alt="{{ $menu->name }}"
                class="menu-img">

            <div class="menu-header">
                <div class="menu-title">{{ $menu->name }}</div>
                <div class="menu-category">{{ $menu->category->name ?? 'Umum' }}</div>
            </div>

            <div class="menu-desc">{{ Str::limit($menu->description, 70) }}</div>

            <div style="margin-bottom:0.875rem;">
                <span class="status-badge {{ $menu->is_available ? 'status-active' : 'status-inactive' }}">
                    <span style="width:6px; height:6px; border-radius:50%; background:currentColor; flex-shrink:0;"></span>
                    {{ $menu->is_available ? 'Tersedia' : 'Tidak Tersedia' }}
                </span>
            </div>

            <div class="menu-footer">
                <div class="menu-price">Rp {{ number_format($menu->price, 0, ',', '.') }}</div>
                <div class="menu-actions">
                    <button @click="openModal('edit', {{ json_encode($menu) }})" class="btn btn-secondary btn-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                        Edit
                    </button>
                    <form action="{{ route('internal.kitchen.menus.toggle', $menu->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ $menu->is_available ? 'btn-danger' : 'btn-success' }}">
                            {{ $menu->is_available ? 'Tutup' : 'Buka' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Add/Edit Modal --}}
    <div class="modal-overlay" :class="{'active': isModalOpen}" @click="closeModal()">
        <div class="modal-card" @click.stop>
            <div class="modal-header">
                <div class="modal-title" x-text="modalMode === 'add' ? 'Tambah Menu Baru' : 'Edit Menu'"></div>
                <button @click="closeModal()" class="close-btn">&times;</button>
            </div>

            <form :action="formAction" method="POST">
                @csrf
                <template x-if="modalMode === 'edit'">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="form-group">
                    <label class="form-label">Nama Menu</label>
                    <input type="text" name="name" class="form-control" x-model="formData.name" required placeholder="Contoh: Nasi Goreng Spesial">
                </div>

                <div class="form-group">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" class="form-control" x-model="formData.category_id" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Harga (Rp)</label>
                    <input type="number" name="price" class="form-control" x-model="formData.price" required min="0" placeholder="15000">
                </div>

                <div class="form-group">
                    <label class="form-label">URL Gambar <span style="font-weight:500; color:var(--text-muted);">(opsional)</span></label>
                    <input type="url" name="image" class="form-control" x-model="formData.image" placeholder="https://...">
                    <div style="font-size:0.75rem; color:var(--text-muted); margin-top:0.3rem;">Kosongkan untuk menggunakan gambar placeholder</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control" x-model="formData.description" rows="3" placeholder="Deskripsi singkat menu..."></textarea>
                </div>

                <div style="display:flex; gap:0.75rem; justify-content:flex-end; margin-top:1.5rem; padding-top:1rem; border-top:1px solid var(--border-light);">
                    <button type="button" @click="closeModal()" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-primary" x-text="modalMode === 'add' ? 'Simpan Menu' : 'Update Menu'"></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('menuManager', () => ({
        isModalOpen: false,
        modalMode: 'add',
        formAction: '{{ route('internal.kitchen.menus.store') }}',
        formData: {
            id: null, name: '', category_id: '', price: '', image: '', description: ''
        },

        openModal(mode, data = null) {
            this.modalMode = mode;
            if (mode === 'edit' && data) {
                this.formData   = { ...data };
                this.formAction = `/internal/kitchen/menus/${data.id}`;
            } else {
                this.formData   = { id: null, name: '', category_id: '', price: '', image: '', description: '' };
                this.formAction = '{{ route('internal.kitchen.menus.store') }}';
            }
            this.isModalOpen = true;
        },

        closeModal() { this.isModalOpen = false; }
    }));
});
</script>
@endsection