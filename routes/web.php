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
use Illuminate\Support\Facades\Mail;

Route::middleware(['auth', 'verified'])->group(function () {
    // Carrito, pedidos, perfil, etc.
    Route::get('/cart', [OrderController::class, 'cart'])->name('cart');
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    // ...
});
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
Route::get('/test-mail', function () {
    try {
        Mail::raw('Correo de prueba desde Mailtrap', function ($message) {
            $message->to('fabriziogarciaalva18@gmail.com')
                    ->subject('Prueba Mailtrap');
        });

        return '¡Correo enviado! Revisa tu bandeja de Mailtrap.';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});
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
Route::get('/products', [AdminProductController::class, 'index'])->name('products');
Route::prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Pedidos
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders');
    Route::put('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');

    // Productos (CRUD) - nombre 'products' para la lista
    Route::get('/products', [AdminProductController::class, 'index'])->name('products');
    Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
    Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');
    Route::patch('/products/{product}/toggle-status', [AdminProductController::class, 'toggleStatus'])->name('products.toggle-status');

    // Pagos pendientes
    Route::get('/payments', [AdminController::class, 'payments'])->name('payments');
});
// ========== RUTAS PÚBLICAS ==========
Route::get('/', function () {
    return view('home');
})->name('home');
Route::get('/catalog', [ProductController::class, 'index'])->name('catalog');
Route::get('/product/{product}', [ProductController::class, 'show'])->name('products.show');
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders');
    Route::put('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');

    // Productos (usando AdminProductController)
    Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
    Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');
    Route::patch('/products/{product}/toggle-status', [AdminProductController::class, 'toggleStatus'])->name('products.toggle-status');

    Route::get('/payments', [AdminController::class, 'payments'])->name('payments');
});
// ========== RUTAS AUTENTICADAS (CLIENTES) ==========
Route::middleware(['auth'])->group(function () {
    // Carrito
    Route::get('/cart', [OrderController::class, 'cart'])->name('cart');
    Route::post('/cart/add', [OrderController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/remove/{id}', [OrderController::class, 'removeFromCart'])->name('cart.remove');
    Route::post('/cart/update/{id}', [OrderController::class, 'updateCart'])->name('cart.update');

    // Checkout
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/checkout/process', [OrderController::class, 'processPayment'])->name('checkout.process');

    // Órdenes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    // Pagos
    Route::get('/payment/{order}', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/{order}/voucher', [PaymentController::class, 'uploadVoucher'])->name('payment.voucher');
    Route::post('/payment/{order}/mark-paid', [PaymentController::class, 'markAsPaid'])->name('payment.mark-paid');
    Route::post('/payment/{order}/confirm', [PaymentController::class, 'confirmOrder'])->name('payment.confirm');
    Route::delete('/payment/{order}/cancel', [PaymentController::class, 'cancelOrder'])->name('payment.cancel');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/avatar/upload', [AvatarController::class, 'upload'])->name('avatar.upload');
    Route::delete('/avatar/remove', [AvatarController::class, 'remove'])->name('avatar.remove');
    Route::put('/password', [App\Http\Controllers\Auth\PasswordController::class, 'update'])->name('password.update');

    // ========== RUTAS ADMIN (con verificación dentro del controlador, sin middleware 'admin') ==========
    Route::prefix('admin')->name('admin.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // Gestión de pedidos
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders');
        Route::put('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');

        // Gestión de productos (CRUD completo)
        Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
        Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');
        Route::patch('/products/{product}/toggle-status', [AdminProductController::class, 'toggleStatus'])->name('products.toggle-status');

        // Gestión de pagos pendientes
        Route::get('/payments', [AdminController::class, 'payments'])->name('payments');
    });
});
Route::middleware(['auth', 'verified'])->group(function () {
    // Carrito
    Route::get('/cart', [OrderController::class, 'cart'])->name('cart');
    Route::post('/cart/add', [OrderController::class, 'addToCart'])->name('cart.add');
    // ... resto de rutas protegidas
});
require __DIR__.'/auth.php';
