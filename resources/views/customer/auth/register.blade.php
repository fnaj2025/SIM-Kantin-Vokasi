<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - KantinVokasi</title>
    <link rel="stylesheet" href="{{ asset('css/customer.css') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <style>
        .auth-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 100vh;
        }
        
        .auth-banner {
            background: linear-gradient(135deg, var(--primary), var(--primary-hover));
            color: white;
            padding: 4rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .auth-banner::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('https://images.unsplash.com/photo-1555396273-367ea4eb4db5?q=80&w=1974&auto=format&fit=crop') center/cover;
            opacity: 0.15;
            mix-blend-mode: overlay;
        }
        
        .auth-banner-content {
            position: relative;
            z-index: 10;
        }
        
        .auth-banner h1 {
            font-size: 3rem;
            color: white;
            margin-bottom: 1rem;
        }
        
        .auth-banner p {
            font-size: 1.25rem;
            opacity: 0.9;
            max-width: 80%;
        }
        
        .auth-form-container {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4rem 2rem;
            background: var(--bg-main);
        }
        
        .auth-card {
            width: 100%;
            max-width: 500px;
            background: white;
            padding: 3rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-color);
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .auth-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            background: var(--primary-light);
            color: var(--primary);
            border-radius: 1rem;
            margin-bottom: 1rem;
        }
        
        .password-input-group {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .password-toggle:hover {
            color: var(--text-dark);
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        @media (max-width: 992px) {
            .auth-layout { grid-template-columns: 1fr; }
            .auth-banner { display: none; }
        }
        @media (max-width: 576px) {
            .form-grid { grid-template-columns: 1fr; gap: 0; }
        }
    </style>
</head>
<body>
    <div class="auth-layout">
        <!-- Banner Side -->
        <div class="auth-banner">
            <div class="auth-banner-content">
                <h1>Bergabung dengan Kami</h1>
                <p>Buat akun sekarang dan nikmati pengalaman baru memesan makanan di Kantin Vokasi.</p>
                
                <div style="margin-top: 3rem; display: flex; flex-direction: column; gap: 1.5rem;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        </div>
                        <div style="font-size: 1.1rem; font-weight: 600;">Proses Cepat & Mudah</div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"></rect><line x1="2" x2="22" y1="10" y2="10"></line></svg>
                        </div>
                        <div style="font-size: 1.1rem; font-weight: 600;">Pembayaran Digital Aman</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Form Side -->
        <div class="auth-form-container">
            <div class="auth-card animate-slide-up">
                <div class="auth-header">
                    <h2>Buat Akun Baru</h2>
                    <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 0.5rem;">Lengkapi data diri Anda di bawah ini</p>
                </div>
                
                @if($errors->any())
                <div style="background: var(--danger); color: white; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; font-size: 0.9rem;">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
                @endif
                
                <form action="{{ route('customer.register') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Masukkan nama lengkap">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Alamat Email *</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="contoh@email.com">
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">No. HP / WhatsApp</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="08...">
                        </div>
                        <div class="form-group">
                            <label class="form-label">NIM (Mahasiswa)</label>
                            <input type="text" name="nim" class="form-control" value="{{ old('nim') }}" placeholder="Opsional">
                        </div>
                    </div>
                    
                    <div class="form-grid" x-data="{ show: false }">
                        <div class="form-group">
                            <label class="form-label">Password *</label>
                            <div class="password-input-group">
                                <input :type="show ? 'text' : 'password'" name="password" class="form-control" required placeholder="Min 8 karakter">
                                <button type="button" class="password-toggle" @click="show = !show" tabindex="-1">
                                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    <svg x-show="show" style="display: none;" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"></path><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"></path><line x1="2" x2="22" y1="2" y2="22"></line></svg>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Konfirmasi Password *</label>
                            <div class="password-input-group">
                                <input :type="show ? 'text' : 'password'" name="password_confirmation" class="form-control" required placeholder="Ulangi password">
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem; padding: 1rem;">
                        Daftar Sekarang
                    </button>
                </form>
                
                <div style="text-align: center; margin-top: 2rem; color: var(--text-muted); font-size: 0.95rem;">
                    Sudah punya akun? <a href="{{ route('customer.login') }}" style="color: var(--primary); font-weight: 700;">Masuk di sini</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
