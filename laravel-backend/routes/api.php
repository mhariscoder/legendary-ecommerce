<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductAttributeController;
use App\Http\Controllers\AttributeVariationController;
use App\Http\Controllers\ProductVariationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CustomerController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::apiResource('products', ProductController::class);
    Route::apiResource('product-attributes', ProductAttributeController::class);
    Route::apiResource('attribute-variations', AttributeVariationController::class);
    Route::apiResource('product-variations', ProductVariationController::class);
    Route::apiResource('product-variation-attributes', ProductVariationAttributeController::class);
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('orders', OrderController::class);
});