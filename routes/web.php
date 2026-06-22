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
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// =====================================================
// 1. RUTAS PÚBLICAS
// =====================================================
Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/catalog', [ProductController::class, 'index'])->name('catalog');
Route::get('/product/{product}', [ProductController::class, 'show'])->name('products.show');

// =====================================================
// 2. VERIFICACIÓN DE EMAIL
// =====================================================
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/home');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('success', 'Enlace de verificación reenviado');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// =====================================================
// 3. RUTAS AUTENTICADAS (CLIENTES)
// =====================================================
Route::middleware(['auth'])->group(function () {

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
    // Mostrar la página de pago según el método elegido (Yape, Plin, Transferencia, Contraentrega)
    Route::get('/payment/method/{method}', [PaymentController::class, 'showPayment'])->name('payment.method');

    // Subir comprobante (para Yape, Plin, Transferencia)
    Route::post('/payment/voucher', [PaymentController::class, 'uploadVoucher'])->name('payment.voucher');

    // Confirmar pedido contraentrega (no requiere comprobante)
    Route::post('/payment/confirm', [PaymentController::class, 'confirmOrder'])->name('payment.confirm');

    // Admin: marcar pago como pagado
    Route::post('/payment/{order}/mark-paid', [PaymentController::class, 'markAsPaid'])->name('payment.mark-paid');

    // Cancelar un pedido desde la página de pago
    Route::delete('/payment/{order}/cancel', [PaymentController::class, 'cancelOrder'])->name('payment.cancel');
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders');
    // ...
});
    // ---- PERFIL ----
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/avatar/upload', [AvatarController::class, 'upload'])->name('avatar.upload');
    Route::delete('/avatar/remove', [AvatarController::class, 'remove'])->name('avatar.remove');
    Route::put('/password', [App\Http\Controllers\Auth\PasswordController::class, 'update'])->name('password.update');

    // ---- ADMIN (verificación dentro del controlador) ----
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders');
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
// 4. RUTAS DE AUTENTICACIÓN (LARAVEL BREEZE)
// =====================================================
require __DIR__.'/auth.php';
