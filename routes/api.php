<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/cart/add', [OrderController::class, 'addToCart']);
    Route::get('/cart/count', [OrderController::class, 'getCartCount']);
});