<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AvatarController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use Illuminate\Support\Facades\Route;

// =====================================================
// 1. RUTAS PÚBLICAS (no requieren autenticación ni verificación)
// =====================================================
Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/catalog', [ProductController::class, 'index'])->name('catalog');
Route::get('/product/{product}', [ProductController::class, 'show'])->name('products.show');

// =====================================================
// 2. RUTAS QUE REQUIEREN AUTENTICACIÓN Y VERIFICACIÓN DE EMAIL
// =====================================================
Route::middleware(['auth', 'verified'])->group(function () {

    // ---- CARRITO ----
    Route::get('/cart', [OrderController::class, 'cart'])->name('cart');
    Route::post('/cart/add', [OrderController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/remove/{id}', [OrderController::class, 'removeFromCart'])->name('cart.remove');
    Route::post('/cart/update/{id}', [OrderController::class, 'updateCart'])->name('cart.update');

    // ---- CHECKOUT ----
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/checkout/process', [OrderController::class, 'processPayment'])->name('checkout.process');

    // ---- ÓRDENES ----
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    // ---- PAGOS ----
    Route::get('/payment/method/{method}', [PaymentController::class, 'showPayment'])->name('payment.method');
    Route::post('/payment/voucher', [PaymentController::class, 'uploadVoucher'])->name('payment.voucher');
    Route::post('/payment/confirm', [PaymentController::class, 'confirmOrder'])->name('payment.confirm');
    Route::post('/payment/{order}/mark-paid', [PaymentController::class, 'markAsPaid'])->name('payment.mark-paid');
    Route::delete('/payment/{order}/cancel', [PaymentController::class, 'cancelOrder'])->name('payment.cancel');

    // ---- PERFIL ----
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/avatar/upload', [AvatarController::class, 'upload'])->name('avatar.upload');
    Route::delete('/avatar/remove', [AvatarController::class, 'remove'])->name('avatar.remove');
    Route::put('/password', [App\Http\Controllers\Auth\PasswordController::class, 'update'])->name('password.update');

    // ---- ADMIN ----
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::put('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');

        // Productos
        Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
        Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');
        Route::patch('/products/{product}/toggle-status', [AdminProductController::class, 'toggleStatus'])->name('products.toggle-status');

        Route::get('/payments', [AdminController::class, 'payments'])->name('payments');
    });
});

// =====================================================
// 3. RUTAS DE AUTENTICACIÓN (LARAVEL BREEZE)
// =====================================================
require __DIR__.'/auth.php';