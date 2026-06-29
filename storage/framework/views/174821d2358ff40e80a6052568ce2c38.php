<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(config('app.name')); ?> Internal — <?php echo $__env->yieldContent('title', 'Dashboard'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('css/internal.css')); ?>">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>

<?php if(auth()->guard()->check()): ?>
<?php
    $role = Auth::user()->role;
    $currentRoute = request()->route()->getName();

    $navItems = [
        [
            'route'  => 'internal.dashboard',
            'label'  => 'Dashboard',
            'roles'  => ['manager','admin_operasional','kasir','kitchen','finance','purchasing'],
            'icon'   => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>',
        ],
        [
            'route'  => 'internal.pos',
            'label'  => 'Point of Sales',
            'roles'  => ['manager','kasir','admin_operasional'],
            'icon'   => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>',
        ],
        [
            'route'  => 'internal.kitchen',
            'label'  => 'Kitchen Display',
            'roles'  => ['manager','kitchen','admin_operasional'],
            'icon'   => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"/><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/></svg>',
            'exact'  => true,
        ],
        [
            'route'  => 'internal.kitchen.menus',
            'label'  => 'Manajemen Menu',
            'roles'  => ['manager','kitchen','admin_operasional'],
            'icon'   => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>',
            'exact'  => true,
        ],
        [
            'route'  => 'internal.orders.index',
            'label'  => 'Riwayat Order',
            'roles'  => ['manager','admin_operasional','kasir'],
            'icon'   => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>',
        ],
        [
            'route'  => 'internal.inventory.index',
            'label'  => 'Inventori',
            'roles'  => ['manager','purchasing','admin_operasional'],
            'icon'   => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>',
        ],
        [
            'route'  => 'internal.finance.index',
            'label'  => 'Keuangan',
            'roles'  => ['manager','finance'],
            'icon'   => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
        ],
        [
            'route'  => 'internal.reports',
            'label'  => 'Laporan & Analitik',
            'roles'  => ['manager','finance'],
            'icon'   => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>',
        ],
    ];
?>

<aside class="sidebar">
    
    <div class="sidebar-brand">
        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/><path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"/></svg>
        <div>
            <div class="sidebar-brand-text">KantinVokasi</div>
            <div class="sidebar-brand-sub">Internal System</div>
        </div>
    </div>

    
    <div style="padding: 1rem 1.25rem 0.5rem;">
        <span class="role-badge">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
            <?php echo e(strtoupper(str_replace('_', ' ', $role))); ?>

        </span>
    </div>

    <p class="sidebar-section">Menu Operasional</p>

    <nav class="sidebar-nav">
        <?php $__currentLoopData = $navItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(in_array($role, $item['roles'])): ?>
            <?php
                $isActive = isset($item['exact']) && $item['exact']
                    ? $currentRoute === $item['route']
                    : request()->routeIs($item['route'] . '*');
            ?>
            <a href="<?php echo e(route($item['route'])); ?>"
               class="sidebar-link <?php echo e($isActive ? 'active' : ''); ?>">
                <span class="link-icon"><?php echo $item['icon']; ?></span>
                <?php echo e($item['label']); ?>

            </a>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </nav>

    
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-avatar"><?php echo e(mb_substr(Auth::user()->name, 0, 1)); ?></div>
            <div class="sidebar-user-info">
                <div class="name"><?php echo e(Auth::user()->name); ?></div>
                <div class="role"><?php echo e(str_replace('_', ' ', Auth::user()->role)); ?></div>
            </div>
        </div>
    </div>
</aside>


<div class="main-wrapper">
    <header class="topbar">
        <div>
            <div class="topbar-title"><?php echo $__env->yieldContent('title', 'Dashboard'); ?></div>
            <div class="topbar-breadcrumb">KantinVokasi &rsaquo; <?php echo $__env->yieldContent('title', 'Dashboard'); ?></div>
        </div>
        <div class="topbar-spacer"></div>
        <span class="topbar-clock"><?php echo e(now()->isoFormat('ddd, D MMM YYYY · HH:mm')); ?></span>
        <form action="<?php echo e(route('internal.logout')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <button type="submit" class="topbar-logout">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Logout
            </button>
        </form>
    </header>

    <main class="page-content">
        
        <?php if(session('success')): ?>
        <div class="flash-success">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <?php echo e(session('success')); ?>

        </div>
        <?php endif; ?>

        
        <?php if(session('error')): ?>
        <div class="flash-error">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <?php echo e(session('error')); ?>

        </div>
        <?php endif; ?>

        
        <?php if($errors->any()): ?>
        <div class="flash-error">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <div>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div><?php echo e($error); ?></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </main>
</div>

<?php else: ?>
    <?php echo $__env->yieldContent('content'); ?>
<?php endif; ?>

<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\xampp\htdocs\KANTINVOKASI\resources\views/layouts/internal.blade.php ENDPATH**/ ?>