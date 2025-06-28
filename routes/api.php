<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Middleware\LogApiActivity;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\OrderController;
     //Authorization
Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1');
        Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
        Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth:sanctum');
        Route::get('user', [AuthController::class, 'user'])->middleware('auth:sanctum');

        Route::post('password/forgot', [AuthController::class, 'forgotPassword'])->middleware('throttle:3,1');
        Route::post('password/reset', [AuthController::class, 'resetPassword']);
    });
          
     //Dashboard Analytics
    Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    Route::prefix('dashboard')->group(function () {
        Route::get('summary', [DashboardController::class, 'summary']);
        Route::get('sales-performance', [DashboardController::class, 'salesPerformance']);
        Route::get('inventory-status', [DashboardController::class, 'inventoryStatus']);
        Route::get('top-products', [DashboardController::class, 'topProducts']);
    });
});
       //Products
Route::apiResource('products', ProductController::class)
    ->except(['create', 'edit']);

Route::prefix('products')->group(function () {
    Route::get('/{product}/stock', [ProductController::class, 'stock']);
    Route::post('/{product}/reserve', [ProductController::class, 'reserveStock']);
    Route::post('/{product}/release', [ProductController::class, 'releaseStock']);
    Route::get('/low-stock', [ProductController::class, 'lowStock']);
});

           //Order
        Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']); // GET /api/v1/orders
        Route::get('{id}', [OrderController::class, 'show']); // GET /api/v1/orders/{id}
        Route::post('/', [OrderController::class, 'store']); // POST /api/v1/orders
        Route::put('{id}/status', [OrderController::class, 'updateStatus']); // PUT /api/v1/orders/{id}/status
        Route::get('{id}/invoice', [OrderController::class, 'invoice']); // GET /api/v1/orders/{id}/invoice
        Route::post('/calculate-total', [OrderController::class, 'calculateTotal']); // POST /api/v1/orders/calculate-total
    });
});

});

