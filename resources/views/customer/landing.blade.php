@extends('layouts.customer')
@section('title', 'Selamat Datang')

@section('content')
<style>
/* Hero Section */
.hero-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: center;
    padding: 4rem 0;
    min-height: 80vh;
}

.hero-content h1 {
    font-size: 3.5rem;
    line-height: 1.1;
    margin-bottom: 1.5rem;
    color: var(--text-dark);
}

.hero-content h1 span {
    color: var(--primary);
    position: relative;
    display: inline-block;
}

.hero-content h1 span::after {
    content: '';
    position: absolute;
    bottom: 8px;
    left: 0;
    width: 100%;
    height: 8px;
    background: var(--primary-light);
    z-index: -1;
    border-radius: 4px;
}

.hero-content p {
    font-size: 1.125rem;
    color: var(--text-muted);
    margin-bottom: 2.5rem;
    max-width: 90%;
}

.hero-image-wrapper {
    position: relative;
    border-radius: 2rem;
    overflow: hidden;
    box-shadow: 0 25px 50px -12px rgba(234, 88, 12, 0.25);
}

.hero-image {
    width: 100%;
    height: auto;
    display: block;
    transform: scale(1.05);
    transition: 0.5s ease;
}

.hero-image-wrapper:hover .hero-image {
    transform: scale(1);
}

/* Features Section */
.features-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
    margin: 4rem 0;
}

.feature-card {
    background: white;
    padding: 2.5rem 2rem;
    border-radius: 1.5rem;
    text-align: center;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-color);
    transition: 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-10px);
    box-shadow: var(--shadow-lg);
    border-color: var(--primary-light);
}

.feature-icon {
    width: 64px;
    height: 64px;
    background: var(--primary-light);
    color: var(--primary);
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
}

.feature-card h3 {
    font-size: 1.25rem;
    margin-bottom: 0.75rem;
}

.feature-card p {
    color: var(--text-muted);
    font-size: 0.95rem;
}

/* Section Heading */
.section-heading {
    text-align: center;
    margin-bottom: 3rem;
}

.section-heading h2 {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
}

.section-heading p {
    color: var(--text-muted);
    font-size: 1.1rem;
}

/* Promo Banner */
.promo-banner {
    background: linear-gradient(135deg, var(--primary), var(--primary-hover));
    border-radius: 2rem;
    padding: 4rem;
    color: white;
    text-align: center;
    position: relative;
    overflow: hidden;
    margin: 6rem 0;
    box-shadow: var(--shadow-orange);
}

.promo-banner::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -10%;
    width: 50%;
    height: 200%;
    background: rgba(255, 255, 255, 0.1);
    transform: rotate(15deg);
}

@media (max-width: 768px) {
    .hero-section {
        grid-template-columns: 1fr;
        padding: 2rem 0;
        text-align: center;
    }
    .hero-content p {
        margin: 0 auto 2rem;
    }
    .features-grid {
        grid-template-columns: 1fr;
    }
    .promo-banner {
        padding: 2rem;
    }
}
</style>

<div class="animate-slide-up">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1>Solusi Lapar Nomor <span>#Satu</span> Ketika di Kampus</h1>
            <p>Digitalisasi Kantin Kampus Lebih Cepat & Modern. Pesan makanan dan minuman favoritmu tanpa perlu antre panjang lagi.</p>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <a href="{{ route('menu') }}" class="btn btn-primary" style="padding: 1rem 2rem; font-size: 1.1rem;">
                    Pesan Sekarang
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                </a>
                <a href="#features" class="btn btn-outline" style="padding: 1rem 2rem; font-size: 1.1rem;">
                    Lihat Menu
                </a>
            </div>
        </div>
        <div class="hero-image-wrapper delay-1">
            <img src="https://timesindonesia.co.id/_next/image?url=https%3A%2F%2Fcdn-1.timesmedia.co.id%2Fimages%2F2022%2F11%2F08%2FKantin.jpg&w=750&q=70" alt="Kantin Vokasi Unesa" class="hero-image">
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="delay-2">
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"></path><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                </div>
                <h3>Pembayaran Mudah</h3>
                <p>Dukung pembayaran non-tunai via QRIS. Cepat, aman, dan langsung terverifikasi oleh kasir.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
                <h3>Tanpa Antre</h3>
                <p>Pesan dari mana saja, pantau status pesanan realtime, dan ambil pesanan saat sudah siap.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                </div>
                <h3>Realtime Update</h3>
                <p>Status pesanan langsung terhubung ke dapur, sehingga waktu tunggu Anda lebih efisien.</p>
            </div>
        </div>
    </section>

    <!-- Top Menus -->
    <section style="margin: 6rem 0;" class="delay-3">
        <div class="section-heading">
            <h2>Rekomendasi Menu</h2>
            <p>Pilihan terfavorit mahasiswa bulan ini</p>
        </div>
        
        <div class="menu-grid">
            @foreach($topMenus as $item)
            <a href="{{ route('menu') }}" class="menu-card" style="color: inherit; text-decoration: none;">
                <div class="menu-img-container">
                    <img src="{{ $item->image ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&q=80' }}" alt="{{ $item->name }}" class="menu-img">
                    <div class="menu-badge">{{ $item->category->name ?? 'Menu' }}</div>
                </div>
                <div class="menu-info">
                    <h3 class="menu-title">{{ $item->name }}</h3>
                    <p class="menu-desc">{{ Str::limit($item->description, 60) }}</p>
                    <div class="menu-footer">
                        <div class="menu-price">Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                        <div class="btn btn-primary" style="padding: 0.5rem; border-radius: 50%;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        
        <div class="text-center" style="margin-top: 3rem;">
            <a href="{{ route('menu') }}" class="btn btn-outline" style="padding: 1rem 3rem;">Lihat Semua Menu</a>
        </div>
    </section>

    <!-- Promo -->
    <section class="promo-banner">
        <div style="position: relative; z-index: 10;">
            <h2 style="font-size: 3rem; margin-bottom: 1rem; color: white;">Promo Khusus Mahasiswa!</h2>
            <p style="font-size: 1.25rem; margin-bottom: 2rem; opacity: 0.9;">Gunakan pembayaran QRIS dan dapatkan layanan prioritas tanpa antre kasir.</p>
            <a href="{{ route('menu') }}" class="btn" style="background: white; color: var(--primary); padding: 1rem 2.5rem; font-size: 1.1rem; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">Mulai Pesan</a>
        </div>
    </section>
</div>
@endsection
