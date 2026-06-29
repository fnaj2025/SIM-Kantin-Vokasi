@extends('layouts.customer')
@section('title', 'Keranjang Pesanan')

@section('content')
<div style="margin-top: 2rem;" class="animate-slide-up">
    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
        <a href="{{ route('menu') }}" class="btn btn-outline" style="padding: 0.5rem; border-radius: 50%;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"></path></svg>
        </a>
        <h2 style="margin: 0; font-size: 2rem;">Keranjang Anda</h2>
    </div>

    @if(session('success'))
    <div style="background: var(--success); color: white; padding: 1rem 1.5rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; display:flex; align-items:center; gap:0.5rem; font-weight:600;">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        {{ session('success') }}
    </div>
    @endif

    @if(empty($cart))
        <div style="text-align: center; padding: 6rem 2rem; background: white; border-radius: var(--radius-lg); border: 1px dashed var(--border-color); box-shadow: var(--shadow-sm);">
            <div style="background: var(--bg-main); width: 96px; height: 96px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-muted"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
            </div>
            <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Keranjang Masih Kosong</h3>
            <p style="color: var(--text-muted); margin-bottom: 2rem;">Sepertinya Anda belum menambahkan menu apapun.</p>
            <a href="{{ route('menu') }}" class="btn btn-primary" style="padding: 1rem 2.5rem;">Lihat Menu</a>
        </div>
    @else
        <div class="cart-layout">
            <div class="cart-items">
                @foreach($cart as $id => $item)
                <div class="cart-item">
                    {{-- ── FIX: gunakan $item['image'] dari session, fallback placeholder ── --}}
                    <img
                        src="{{ !empty($item['image']) ? $item['image'] : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=150&q=80' }}"
                        alt="{{ $item['name'] }}"
                        class="cart-item-img"
                        style="object-fit: cover; border-radius: 0.75rem;"
                        onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=150&q=80'">

                    <div class="cart-item-info">
                        <div class="cart-item-title">{{ $item['name'] }}</div>
                        <div class="cart-item-price">Rp {{ number_format($item['price'], 0, ',', '.') }}</div>
                        @if(!empty($item['notes']))
                            <div style="font-size:0.78rem; color:var(--text-muted); margin-top:0.25rem;">
                                Catatan: {{ $item['notes'] }}
                            </div>
                        @endif
                    </div>

                    <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 1rem;">
                        {{-- Tombol hapus --}}
                        <form action="{{ route('cart.remove') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id" value="{{ $id }}">
                            <button type="submit" style="background:none; border:none; color:var(--danger); cursor:pointer; padding:0.25rem;" title="Hapus Item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path></svg>
                            </button>
                        </form>

                        {{-- ── FIX qty control: gunakan JS submit agar tidak ada navigasi ganda ── --}}
                        <div class="qty-control" x-data="{ qty: {{ $item['quantity'] }} }">
                            <form action="{{ route('cart.update') }}" method="POST" id="qty-form-{{ $id }}">
                                @csrf
                                <input type="hidden" name="id" value="{{ $id }}">
                                <input type="hidden" name="quantity" :value="qty" id="qty-val-{{ $id }}">
                            </form>
                            <button type="button" class="qty-btn"
                                @click="if(qty > 1) { qty--; $nextTick(() => document.getElementById('qty-form-{{ $id }}').submit()) }">-</button>
                            <span class="qty-input" style="display:flex; align-items:center; justify-content:center; min-width:40px; font-weight:700;" x-text="qty"></span>
                            <button type="button" class="qty-btn"
                                @click="qty++; $nextTick(() => document.getElementById('qty-form-{{ $id }}').submit())">+</button>
                        </div>

                        {{-- Subtotal per item --}}
                        <div style="font-size:0.85rem; color:var(--text-muted); font-weight:600;">
                            Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Summary --}}
            <div class="cart-summary delay-1">
                <div class="summary-card">
                    <h3 style="margin-bottom: 1.5rem; font-size: 1.25rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">Ringkasan Pesanan</h3>

                    @php
                        $subtotal   = collect($cart)->sum('subtotal');
                        $totalItems = collect($cart)->sum('quantity');
                    @endphp

                    <div class="summary-row">
                        <span>Total Item</span>
                        <span style="font-weight: 600; color: var(--text-dark);">{{ $totalItems }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Estimasi Waktu</span>
                        <span style="font-weight: 600; color: var(--text-dark);">~15 Menit</span>
                    </div>

                    <div class="summary-total">
                        <span>Total Bayar</span>
                        <span class="text-primary">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>

                    <a href="{{ route('checkout') }}" class="btn btn-primary" style="width: 100%; margin-top: 2rem; padding: 1rem;">
                        Lanjut ke Pembayaran
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                    </a>
                </div>

                <div style="margin-top: 1rem; text-align: center; font-size: 0.85rem; color: var(--text-muted);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 0.25rem;"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    Pembayaran aman dan terenkripsi
                </div>
            </div>
        </div>
    @endif
</div>
@endsection