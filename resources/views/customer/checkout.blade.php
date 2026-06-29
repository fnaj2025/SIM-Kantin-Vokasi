@extends('layouts.customer')
@section('title', 'Checkout')

@section('content')
<style>
/* Checkout Layout */
.checkout-layout {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 3rem;
    align-items: start;
    margin-top: 2rem;
}

.checkout-card {
    background: white;
    border-radius: var(--radius-lg);
    padding: 2.5rem;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-color);
}

.section-title {
    font-size: 1.25rem;
    font-weight: 800;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Radio Cards */
.radio-card-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 2rem;
}

.radio-card {
    position: relative;
    border: 2px solid var(--border-color);
    border-radius: var(--radius-md);
    padding: 1.5rem 1rem;
    cursor: pointer;
    transition: all 0.2s;
    text-align: center;
    background: var(--bg-main);
}

.radio-card:hover { border-color: var(--primary-light); background: white; }

.radio-card input[type="radio"] { position: absolute; opacity: 0; }

.radio-card input[type="radio"]:checked + .radio-content { color: var(--primary); }

.radio-card:has(input[type="radio"]:checked) {
    border-color: var(--primary);
    background: white;
    box-shadow: 0 0 0 4px rgba(234, 88, 12, 0.1);
}

.radio-icon {
    width: 40px; height: 40px;
    margin: 0 auto 0.75rem;
    display: flex; align-items: center; justify-content: center;
    color: var(--text-muted);
    transition: color 0.2s;
}

.radio-card input[type="radio"]:checked + .radio-content .radio-icon { color: var(--primary); }

.radio-title  { font-weight: 700; font-size: 1rem; margin-bottom: 0.25rem; }
.radio-desc   { font-size: 0.75rem; color: var(--text-muted); }

/* Catatan pesanan */
.notes-area {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 1.5px solid var(--border-color);
    border-radius: var(--radius-md);
    font-family: inherit;
    font-size: 0.95rem;
    resize: vertical;
    min-height: 80px;
    transition: border-color 0.2s;
}
.notes-area:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(234, 88, 12, 0.08);
}

@media (max-width: 992px) { .checkout-layout { grid-template-columns: 1fr; } }
@media (max-width: 576px) { .radio-card-grid { grid-template-columns: 1fr; } }
</style>

<div class="animate-slide-up" x-data="{ submitted: false }">
    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; margin-top: 2rem;">
        <a href="{{ route('cart.index') }}" class="btn btn-outline" style="padding: 0.5rem; border-radius: 50%;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"></path></svg>
        </a>
        <h2 style="margin: 0; font-size: 2rem;">Checkout</h2>
    </div>

    @if($errors->any())
    <div style="background: #FEF2F2; border: 1.5px solid #FECACA; color: #991B1B; padding: 1rem 1.5rem; border-radius: var(--radius-md); margin-bottom: 2rem; display:flex; gap:0.875rem; align-items:flex-start;">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <div>
            <div style="font-weight: 700; margin-bottom: 0.25rem;">Gagal Memproses Pesanan</div>
            <ul style="margin-left: 1rem; font-size: 0.9rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <div class="checkout-layout">
        {{-- Form Kiri --}}
        <div class="checkout-card">
            <form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm">
                @csrf

                {{-- Info Pemesan --}}
                <h3 class="section-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Informasi Pemesan
                </h3>

                <div style="background: var(--bg-main); padding: 1.5rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); margin-bottom: 2.5rem; display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 48px; height: 48px; background: var(--primary-light); color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.25rem; flex-shrink:0;">
                        {{ mb_substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 1.1rem; color: var(--text-dark);">{{ auth()->user()->name }}</div>
                        <div style="color: var(--text-muted); font-size: 0.85rem;">
                            {{ auth()->user()->email }}
                            @if(auth()->user()->customerProfile?->phone)
                                &bull; {{ auth()->user()->customerProfile->phone }}
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Metode Pembayaran --}}
                <h3 class="section-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    Metode Pembayaran
                </h3>

                <div class="radio-card-grid">
                    <label class="radio-card">
                        <input type="radio" name="payment_method" value="qris" required checked>
                        <div class="radio-content">
                            <div class="radio-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M7 7h.01M17 7h.01M7 17h.01M17 17h.01M12 7h.01M12 12h.01M12 17h.01M7 12h.01M17 12h.01"/></svg>
                            </div>
                            <div class="radio-title">QRIS</div>
                            <div class="radio-desc">Bayar via e-wallet / m-banking</div>
                        </div>
                    </label>
                    <label class="radio-card">
                        <input type="radio" name="payment_method" value="tunai" required>
                        <div class="radio-content">
                            <div class="radio-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/></svg>
                            </div>
                            <div class="radio-title">Tunai</div>
                            <div class="radio-desc">Bayar di kasir secara langsung</div>
                        </div>
                    </label>
                </div>

                {{-- Jenis Pesanan --}}
                <h3 class="section-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><path d="M3 9h18v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9Z"/><path d="m3 9 2.45-4.9A2 2 0 0 1 7.24 3h9.52a2 2 0 0 1 1.8 1.1L21 9"/><path d="M12 3v6"/></svg>
                    Jenis Pesanan
                </h3>

                <div class="radio-card-grid">
                    <label class="radio-card">
                        <input type="radio" name="order_type" value="dine_in" required checked>
                        <div class="radio-content">
                            <div class="radio-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/><path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"/></svg>
                            </div>
                            <div class="radio-title">Makan di Tempat</div>
                            <div class="radio-desc">Dine in di kantin</div>
                        </div>
                    </label>
                    <label class="radio-card">
                        <input type="radio" name="order_type" value="takeaway" required>
                        <div class="radio-content">
                            <div class="radio-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 14 1.45-2.9A2 2 0 0 1 9.24 10H20a2 2 0 0 1 1.94 2.5l-1.55 6a2 2 0 0 1-1.94 1.5H4a2 2 0 0 1-2-2V5c0-1.1.9-2 2-2h3.93a2 2 0 0 1 1.66.9l.82 1.2a2 2 0 0 0 1.66.9H18a2 2 0 0 1 2 2v2"/></svg>
                            </div>
                            <div class="radio-title">Bungkus</div>
                            <div class="radio-desc">Takeaway untuk dibawa pulang</div>
                        </div>
                    </label>
                </div>

                {{-- ── BARU: Catatan / Request Pesanan ── --}}
                <h3 class="section-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    Catatan / Request
                    <span style="font-size: 0.8rem; font-weight: 500; color: var(--text-muted); margin-left:0.5rem;">(Opsional)</span>
                </h3>

                <textarea
                    name="request_notes"
                    class="notes-area"
                    placeholder="Contoh: Tidak pakai cabai, nasi sedikit, kuah dipisah, alergi kacang, dll..."
                    maxlength="500">{{ old('request_notes') }}</textarea>
                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.375rem;">
                    Permintaan khusus akan kami sampaikan ke dapur, namun tidak selalu bisa terpenuhi.
                </div>
            </form>
        </div>

        {{-- Summary Kanan --}}
        <div class="summary-card delay-1">
            <h3 style="margin-bottom: 1.5rem; font-size: 1.25rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">Rincian Pesanan</h3>

            <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1.5rem;">
                @foreach($cart as $item)
                    <div style="display: flex; justify-content: space-between; font-size: 0.9rem;">
                        <div>
                            <span style="font-weight: 700;">{{ $item['quantity'] }}x</span> {{ $item['name'] }}
                            @if(!empty($item['notes']))
                                <div style="font-size:0.75rem; color:var(--text-muted); margin-top:0.1rem;">{{ $item['notes'] }}</div>
                            @endif
                        </div>
                        <div style="font-weight: 600; color: var(--text-muted);">
                            Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="summary-total">
                <span>Total Tagihan</span>
                <span class="text-primary">Rp {{ number_format(collect($cart)->sum('subtotal'), 0, ',', '.') }}</span>
            </div>

            <button type="submit" form="checkoutForm" class="btn btn-primary" style="width: 100%; margin-top: 2rem; padding: 1.25rem; font-size: 1.1rem;">
                Buat Pesanan
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </button>

            <div style="margin-top: 1.25rem; padding: 1rem; background: #FFF7ED; border-radius: var(--radius-md); border: 1px solid #FED7AA; font-size: 0.82rem; color: #9A3412; display:flex; gap:0.625rem; align-items:flex-start;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span>Setelah memesan, <b>tunjukkan bukti pesanan</b> ke kasir untuk verifikasi pembayaran dan pengambilan.</span>
            </div>
        </div>
    </div>
</div>
@endsection