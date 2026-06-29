<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Internal — KantinVokasi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #ea580c;
            --primary-hover: #c2410c;
            --primary-soft: #fff7ed;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --bg-body: #f8fafc;
            --border: #e2e8f0;
            --radius: 1.25rem;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: radial-gradient(circle at top right, #fff7ed, transparent), 
                        radial-gradient(circle at bottom left, #fff7ed, transparent),
                        #f8fafc;
            display: flex; 
            align-items: center; 
            justify-content: center; 
            min-height: 100vh; 
            padding: 1.5rem; 
            color: var(--text-dark);
        }

        .login-card { 
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: var(--radius); 
            padding: 3rem; 
            width: 100%; 
            max-width: 460px; 
            box-shadow: 0 25px 50px -12px rgba(234, 88, 12, 0.1);
            border: 1px solid white;
        }

        .header { 
            text-align: center; 
            margin-bottom: 3rem; 
        }

        .logo-box {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, var(--primary), #fb923c);
            color: white;
            border-radius: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 20px -5px rgba(234, 88, 12, 0.4);
        }

        .title { 
            font-size: 1.75rem; 
            font-weight: 800; 
            letter-spacing: -0.02em;
            margin-bottom: 0.5rem;
        }

        .subtitle { 
            font-size: 0.95rem; 
            color: var(--text-muted); 
            font-weight: 500; 
        }

        .form-group { 
            margin-bottom: 1.75rem; 
            position: relative;
        }

        .form-label { 
            display: block; 
            font-size: 0.875rem; 
            font-weight: 700; 
            margin-bottom: 0.625rem; 
            color: var(--text-dark);
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 1.25rem;
            color: var(--text-muted);
        }

        .form-input { 
            width: 100%; 
            padding: 1.125rem 1.25rem 1.125rem 3.25rem; 
            border: 1.5px solid var(--border); 
            border-radius: 1rem; 
            font-size: 1rem; 
            font-family: inherit; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            background: white;
        }

        .form-input:focus { 
            outline: none; 
            border-color: var(--primary); 
            box-shadow: 0 0 0 4px var(--primary-soft); 
        }

        .password-toggle {
            position: absolute;
            right: 1.25rem;
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 0.5rem;
            transition: 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .password-toggle:hover {
            color: var(--primary);
            background: var(--primary-soft);
        }

        .btn-submit { 
            width: 100%; 
            padding: 1.125rem; 
            background: var(--primary); 
            color: white; 
            border: none; 
            border-radius: 1rem; 
            font-weight: 800; 
            font-size: 1.1rem; 
            cursor: pointer; 
            transition: all 0.3s; 
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(234, 88, 12, 0.3);
        }

        .btn-submit:hover { 
            background: var(--primary-hover); 
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(234, 88, 12, 0.4);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .alert-box {
            background: #fef2f2;
            border: 1px solid #fee2e2;
            color: #991b1b;
            padding: 1rem 1.25rem;
            border-radius: 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .footer-note { 
            text-align: center; 
            font-size: 0.875rem; 
            color: var(--text-muted); 
            margin-top: 2.5rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border);
            font-weight: 500;
        }

        .demo-accounts {
            margin-top: 1.5rem;
            background: #fafafa;
            border-radius: 1rem;
            padding: 1.25rem;
            border: 1px solid var(--border);
        }

        .demo-title {
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            margin-bottom: 0.75rem;
            display: block;
        }

        .demo-item {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
            padding: 0.5rem 0;
            border-bottom: 1px dashed var(--border);
        }

        .demo-item:last-child { border-bottom: none; }
        .demo-label { font-weight: 700; color: var(--text-dark); }
        .demo-val { color: var(--text-muted); }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="header">
            <div class="logo-box">
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"></path><path d="M7 2v20"></path><path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"></path></svg>
            </div>
            <h1 class="title">KantinVokasi</h1>
            <p class="subtitle">Internal Operation System</p>
        </div>

        <?php if(session('error')): ?>
        <div class="alert-box">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            <?php echo e(session('error')); ?>

        </div>
        <?php endif; ?>

        <form action="<?php echo e(route('internal.login')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label class="form-label">Email Divisi</label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"></rect><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path></svg>
                    </span>
                    <input type="email" name="email" class="form-input" value="<?php echo e(old('email')); ?>" required autofocus placeholder="nama@kantinvokasi.com">
                </div>
                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.5rem; font-weight: 600;"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    </span>
                    <input type="password" id="password" name="password" class="form-input" required placeholder="••••••••">
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg id="eye-off-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display: none;"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                Masuk Sekarang
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
            </button>
        </form>

        <div class="footer-note">
            Hanya untuk staf resmi KantinVokasi
        </div>

        <div class="demo-accounts">
            <span class="demo-title">Akun Demo (password123)</span>
            <div class="demo-item"><span class="demo-label">Manager</span><span class="demo-val">manager@kantinvokasi.com</span></div>
            <div class="demo-item"><span class="demo-label">Finance</span><span class="demo-val">finance@kantinvokasi.com</span></div>
            <div class="demo-item"><span class="demo-label">Dapur</span><span class="demo-val">kitchen@kantinvokasi.com</span></div>
            <div class="demo-item"><span class="demo-label">Kasir</span><span class="demo-val">kasir@kantinvokasi.com</span></div>
            <div class="demo-item"><span class="demo-label">inventory</span><span class="demo-val">purchasing@kantinvokasi.com</span></div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const eye = document.getElementById('eye-icon');
            const eyeOff = document.getElementById('eye-off-icon');
            
            if (input.type === 'password') {
                input.type = 'text';
                eye.style.display = 'none';
                eyeOff.style.display = 'block';
            } else {
                input.type = 'password';
                eye.style.display = 'block';
                eyeOff.style.display = 'none';
            }
        }
    </script>
</body>
</html>

<?php /**PATH C:\xampp\htdocs\KANTINVOKASI\resources\views/internal/login.blade.php ENDPATH**/ ?>