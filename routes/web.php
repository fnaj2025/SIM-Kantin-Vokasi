<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CUSTOMER ROUTES (FRONT OFFICE)
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Customer\MenuController as CustomerMenuController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Customer\OrderHistoryController;
use App\Http\Controllers\Customer\AuthController as CustomerAuthController;

// Customer Auth
Route::prefix('customer')->name('customer.')->group(function() {
    Route::get('/login', [CustomerAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [CustomerAuthController::class, 'login']);
    Route::get('/register', [CustomerAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [CustomerAuthController::class, 'register']);
    Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');
});

// Protected Customer Routes
Route::middleware('customer')->group(function () {
    Route::get('/', [CustomerMenuController::class, 'landing'])->name('home');
    Route::get('/menu', [CustomerMenuController::class, 'index'])->name('menu');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    
    Route::get('/checkout', [CustomerOrderController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [CustomerOrderController::class, 'store'])->name('checkout.store');
    
    Route::get('/orders', [OrderHistoryController::class, 'index'])->name('orders.history');
    Route::get('/customer/profile', [CustomerAuthController::class, 'profile'])->name('customer.profile');
    Route::put('/customer/profile', [CustomerAuthController::class, 'updateProfile'])->name('customer.profile.update');
});


/*
|--------------------------------------------------------------------------
| INTERNAL ROUTES (BACK OFFICE)
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Auth\LoginController as InternalLoginController;
use App\Http\Controllers\Internal\DashboardController;
use App\Http\Controllers\Internal\PosController;
use App\Http\Controllers\Internal\KitchenController;
use App\Http\Controllers\Internal\InventoryController;
use App\Http\Controllers\Internal\FinanceController;
use App\Http\Controllers\Internal\ReportController;
use App\Http\Controllers\Internal\OrderController as InternalOrderController;

Route::prefix('internal')->name('internal.')->group(function () {
    // Auth (No Register)
    Route::get('/login', [InternalLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [InternalLoginController::class, 'login']);
    Route::post('/logout', [InternalLoginController::class, 'logout'])->name('logout');

    // Protected Internal Routes
    Route::middleware('internal')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        Route::get('/pos', [PosController::class, 'index'])->name('pos');
        Route::post('/pos/checkout', [PosController::class, 'store'])->name('pos.store');

        // ── Kitchen Routes ────────────────────────────────────────────────────
        Route::get('/kitchen', [KitchenController::class, 'index'])->name('kitchen');
        Route::patch('/kitchen/{kitchenQueue}/status', [KitchenController::class, 'updateStatus'])->name('kitchen.update-status');
        Route::get('/kitchen/stock-data', [KitchenController::class, 'stockData'])->name('kitchen.stock-data');
        Route::get('/kitchen/{kitchenQueue}/check-stock', [KitchenController::class, 'checkStock'])->name('kitchen.check-stock');

        // ── Kitchen Menu Management ───────────────────────────────────────────
        Route::get('/kitchen/menus', [KitchenController::class, 'menuList'])->name('kitchen.menus');
        Route::post('/kitchen/menus', [KitchenController::class, 'storeMenu'])->name('kitchen.menus.store');
        Route::put('/kitchen/menus/{menuItem}', [KitchenController::class, 'updateMenu'])->name('kitchen.menus.update');
        Route::post('/kitchen/menus/{menuItem}/toggle', [KitchenController::class, 'toggleMenu'])->name('kitchen.menus.toggle');

        // ── Inventory Routes (static before wildcard) ─────────────────────────
        Route::get('/inventory',                        [InventoryController::class, 'index'])->name('inventory.index');
        Route::post('/inventory',                       [InventoryController::class, 'store'])->name('inventory.store');
        Route::post('/inventory/purchase',              [InventoryController::class, 'storePurchase'])->name('inventory.purchase');
        Route::get('/inventory/stock-api',              [InventoryController::class, 'stockApi'])->name('inventory.stock-api');
        Route::put('/inventory/{inventory}',            [InventoryController::class, 'update'])->name('inventory.update');
        Route::delete('/inventory/{inventory}',         [InventoryController::class, 'destroy'])->name('inventory.destroy');
        Route::post('/inventory/{inventory}/restock',   [InventoryController::class, 'restock'])->name('inventory.restock');
        Route::get('/inventory/{inventory}/movements',  [InventoryController::class, 'movements'])->name('inventory.movements');

        // ── Order & Finance Resources ─────────────────────────────────────────
        Route::resource('orders', InternalOrderController::class);
        Route::resource('finance', FinanceController::class);

        // Reimbursement workflow
        Route::post('/finance/reimburse', [FinanceController::class, 'storeReimbursement'])->name('finance.reimburse');
        Route::post('/finance/reimburse/{reimbursement}/approve', [FinanceController::class, 'approveReimbursement'])->name('finance.reimburse.approve');

        Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    });
});
