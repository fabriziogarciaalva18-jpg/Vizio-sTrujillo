<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AvatarController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomizationController;
use App\Http\Controllers\Delivery\DeliveryController;
use App\Http\Middleware\CheckUserActive;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

// =============================================
// RUTAS PÚBLICAS
// =============================================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::middleware(['auth', 'verified', CheckUserActive::class])->prefix('delivery')->name('delivery.')->group(function () {
    Route::get('/dashboard', [DeliveryController::class, 'dashboard'])->name('dashboard');
    Route::post('/take/{order}', [DeliveryController::class, 'takeOrder'])->name('take');
    Route::get('/orders/{order}', [DeliveryController::class, 'show'])->name('show');
    Route::post('/orders/{order}/update-location', [DeliveryController::class, 'updateLocation'])->name('update-location');
    Route::get('/orders/{order}/location', [DeliveryController::class, 'getLocation'])->name('location');
    Route::post('/orders/{order}/confirm', [DeliveryController::class, 'confirmDelivery'])->name('confirm');
    Route::post('/orders/{order}/failed', [DeliveryController::class, 'markAsFailed'])->name('failed');
});
// Ruta para servir avatares (pública)
Route::get('/avatar/{filename}', function ($filename) {
    $path = storage_path('app/public/avatars/' . $filename);
    if (file_exists($path)) {
        return response()->file($path);
    }
    abort(404);
})->name('avatar.serve');

// Catálogo y detalle de producto (públicos)
Route::get('/catalog', [ProductController::class, 'index'])->name('catalog');
Route::get('/product/{product}', [ProductController::class, 'show'])->name('products.show');

// =============================================
// RUTAS AUTENTICADAS
// (requieren login, verificación de email y usuario activo)
// =============================================
Route::middleware(['auth', 'verified', CheckUserActive::class])->group(function () {

    // =========================================
    // PERFIL
    // =========================================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Avatar
    Route::post('/avatar/upload', [AvatarController::class, 'upload'])->name('avatar.upload');
    Route::delete('/avatar/remove', [AvatarController::class, 'remove'])->name('avatar.remove');

    // Cambiar contraseña
    Route::put('/password', [App\Http\Controllers\Auth\PasswordController::class, 'update'])->name('password.update');

    // =========================================
    // CARRITO
    // =========================================
    Route::get('/cart', [OrderController::class, 'cart'])->name('cart');
    Route::post('/cart/add', [OrderController::class, 'addToCart'])->name('cart.add');
    Route::delete('/cart/remove/{id}', [OrderController::class, 'removeFromCart'])->name('cart.remove');
    Route::post('/cart/update/{id}', [OrderController::class, 'updateCart'])->name('cart.update');
    Route::post('/cart/update-item/{key}', [OrderController::class, 'updateCartItem'])->name('cart.update-item');

    // 🔥 API para obtener personalizaciones de un producto (para el modal de edición en el carrito)
    Route::get('/api/product/{product}/customizations', [ProductController::class, 'getCustomizationsData'])->name('api.product.customizations');

    // =========================================
    // CHECKOUT
    // =========================================
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/checkout/process', [OrderController::class, 'processPayment'])->name('checkout.process');

    // =========================================
    // ÓRDENES (usuario)
    // =========================================
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    // =========================================
    // PAGOS
    // =========================================
    Route::get('/payment/method/{method}', [PaymentController::class, 'showPayment'])->name('payment.method');
    Route::post('/payment/voucher', [PaymentController::class, 'uploadVoucher'])->name('payment.voucher');
    Route::post('/payment/confirm', [PaymentController::class, 'confirmOrder'])->name('payment.confirm');
    Route::post('/payment/{order}/mark-paid', [PaymentController::class, 'markAsPaid'])->name('payment.mark-paid');
    Route::delete('/payment/{order}/cancel', [PaymentController::class, 'cancelOrder'])->name('payment.cancel');

    // =========================================
    // REPARTIDOR
    // =========================================
    Route::prefix('delivery')->name('delivery.')->group(function () {
        Route::get('/dashboard', [DeliveryController::class, 'dashboard'])->name('dashboard');
        Route::get('/orders', [DeliveryController::class, 'orders'])->name('orders');
        Route::get('/orders/{order}', [DeliveryController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/confirm', [DeliveryController::class, 'confirmDelivery'])->name('orders.confirm');
        Route::post('/orders/{order}/failed', [DeliveryController::class, 'markAsFailed'])->name('orders.failed');
    });

    // =========================================
    // ADMIN
    // =========================================
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::put('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');

        // ---------- CATEGORÍAS ----------
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        Route::patch('/categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');

        // ---------- PERSONALIZACIONES ----------
        Route::get('/customizations', [CustomizationController::class, 'index'])->name('customizations.index');
        Route::get('/customizations/create', [CustomizationController::class, 'create'])->name('customizations.create');
        Route::post('/customizations', [CustomizationController::class, 'store'])->name('customizations.store');
        Route::get('/customizations/{customization}/edit', [CustomizationController::class, 'edit'])->name('customizations.edit');
        Route::put('/customizations/{customization}', [CustomizationController::class, 'update'])->name('customizations.update');
        Route::delete('/customizations/{customization}', [CustomizationController::class, 'destroy'])->name('customizations.destroy');
        Route::patch('/customizations/{customization}/toggle-status', [CustomizationController::class, 'toggleStatus'])->name('customizations.toggle-status');

        // ---------- PRODUCTOS ----------
        Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
        Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');
        Route::patch('/products/{product}/toggle-status', [AdminProductController::class, 'toggleStatus'])->name('products.toggle-status');

        // ---------- PAGOS PENDIENTES ----------
        Route::get('/payments', [AdminController::class, 'payments'])->name('payments');
    });
});
// =============================================
// RUTAS DEL REPARTIDOR
// =============================================
// Rutas del repartidor
Route::middleware(['auth', 'verified', CheckUserActive::class])->prefix('delivery')->name('delivery.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Delivery\DeliveryController::class, 'dashboard'])->name('dashboard');
    Route::post('/take/{order}', [App\Http\Controllers\Delivery\DeliveryController::class, 'takeOrder'])->name('take');
    Route::get('/orders/{order}', [App\Http\Controllers\Delivery\DeliveryController::class, 'show'])->name('show');
    Route::post('/orders/{order}/update-location', [App\Http\Controllers\Delivery\DeliveryController::class, 'updateLocation'])->name('update-location');
    Route::get('/orders/{order}/location', [App\Http\Controllers\Delivery\DeliveryController::class, 'getLocation'])->name('location');
    Route::post('/orders/{order}/confirm', [App\Http\Controllers\Delivery\DeliveryController::class, 'confirmDelivery'])->name('confirm');
    Route::post('/orders/{order}/failed', [App\Http\Controllers\Delivery\DeliveryController::class, 'markAsFailed'])->name('failed');
});
// =============================================
// RUTAS DE AUTENTICACIÓN (BREEZE)
// =============================================
require __DIR__.'/auth.php';
