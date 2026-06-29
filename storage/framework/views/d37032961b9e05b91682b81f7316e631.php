<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KantinVokasi</title>
    <link rel="stylesheet" href="<?php echo e(asset('css/customer.css')); ?>">
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
            max-width: 450px;
            background: white;
            padding: 3rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-color);
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: 2.5rem;
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

        @media (max-width: 992px) {
            .auth-layout { grid-template-columns: 1fr; }
            .auth-banner { display: none; }
        }
    </style>
</head>
<body>
    <div class="auth-layout">
        <!-- Banner Side -->
        <div class="auth-banner">
            <div class="auth-banner-content">
                <h1>Selamat Datang Kembali</h1>
                <p>Nikmati kemudahan memesan makanan dan minuman favorit Anda di Kantin Vokasi, kapan saja dan dari mana saja.</p>
                
                <div style="margin-top: 3rem; display: flex; gap: 2rem;">
                    <div>
                        <div style="font-size: 2rem; font-weight: 800;">10+</div>
                        <div style="font-size: 0.9rem; opacity: 0.8;">Menu Pilihan</div>
                    </div>
                    <div>
                        <div style="font-size: 2rem; font-weight: 800;">⚡</div>
                        <div style="font-size: 0.9rem; opacity: 0.8;">Tanpa Antre</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Form Side -->
        <div class="auth-form-container">
            <div class="auth-card animate-slide-up">
                <div class="auth-header">
                    <div class="auth-logo">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"></path><path d="M7 2v20"></path><path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"></path></svg>
                    </div>
                    <h2>Masuk ke Akun Anda</h2>
                    <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 0.5rem;">Silakan masukkan email dan password Anda</p>
                </div>
                
                <?php if($errors->any()): ?>
                <div style="background: var(--danger); color: white; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; font-size: 0.9rem;">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div><?php echo e($error); ?></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>
                
                <form action="<?php echo e(route('customer.login')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    
                    <div class="form-group">
                        <label class="form-label">Alamat Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo e(old('email')); ?>" required autofocus placeholder="contoh@email.com">
                    </div>
                    
                    <div class="form-group" x-data="{ show: false }">
                        <label class="form-label">Password</label>
                        <div class="password-input-group">
                            <input :type="show ? 'text' : 'password'" name="password" class="form-control" required placeholder="Masukkan password Anda">
                            <button type="button" class="password-toggle" @click="show = !show">
                                <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                <svg x-show="show" style="display: none;" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"></path><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"></path><line x1="2" x2="22" y1="2" y2="22"></line></svg>
                            </button>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem; padding: 1rem;">
                        Masuk Sekarang
                    </button>
                </form>
                
                <div style="text-align: center; margin-top: 2rem; color: var(--text-muted); font-size: 0.95rem;">
                    Belum punya akun? <a href="<?php echo e(route('customer.register')); ?>" style="color: var(--primary); font-weight: 700;">Daftar di sini</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\KANTINVOKASI\resources\views/customer/auth/login.blade.php ENDPATH**/ ?>