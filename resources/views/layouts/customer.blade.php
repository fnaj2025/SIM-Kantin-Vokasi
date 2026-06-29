<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="KantinVokasi - Sistem Manajemen Kantin Cerdas berbasis Agentic AI">
    <title>KantinVokasi - @yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/customer.css') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
</head>
<body>
    <nav class="navbar">
        <div class="container navbar-container">
            <a href="{{ route('home') }}" class="navbar-logo">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"></path><path d="M7 2v20"></path><path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"></path></svg>
                KantinVokasi
            </a>
            <div class="navbar-links">
                <a href="{{ route('menu') }}" class="navbar-link {{ request()->routeIs('menu') ? 'active' : '' }}">Menu</a>
                <a href="{{ route('orders.history') }}" class="navbar-link {{ request()->routeIs('orders.history') ? 'active' : '' }}">Riwayat Pesanan</a>
                @auth
                    @if(auth()->user()->role === 'customer')
                        <a href="{{ route('customer.profile') }}" class="navbar-link {{ request()->routeIs('customer.profile') ? 'active' : '' }}">Profil Saya</a>
                    @endif
                @else
                    <a href="{{ route('customer.login') }}" class="btn btn-primary" style="padding: 0.5rem 1.25rem;">Login / Daftar</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="container mb-8">
        @yield('content')
    </main>

    <footer style="background: white; border-top: 1px solid var(--border-color); padding: 3rem 0; margin-top: auto;">
        <div class="container text-center">
            <div style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; margin-bottom: 1rem; color: var(--text-muted); font-weight: 600;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"></path><path d="M7 2v20"></path><path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"></path></svg>
                KantinVokasi &copy; {{ date('Y') }}
            </div>
            <p style="color: var(--text-muted); font-size: 0.875rem;">Platform Kantin Cerdas dengan Agentic AI</p>
        </div>
    </footer>

    @php
        $cartCount = count(session('cart', []));
    @endphp

    @if(!request()->routeIs('cart.index') && !request()->routeIs('checkout'))
    <a href="{{ route('cart.index') }}" class="floating-cart animate-slide-up delay-3">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
        @if($cartCount > 0)
        <span class="cart-badge">{{ $cartCount }}</span>
        @endif
    </a>
    @endif

</body>
</html>
