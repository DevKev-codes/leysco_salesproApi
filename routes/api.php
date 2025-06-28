<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\V1\CustomerController;
use App\Http\Controllers\V1\WarehouseController;


// --- Authorization ---
Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1');
        Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
        Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth:sanctum');
        Route::get('user', [AuthController::class, 'user'])->middleware('auth:sanctum');

        Route::post('password/forgot', [AuthController::class, 'forgotPassword'])->middleware('throttle:3,1');
        Route::post('password/reset', [AuthController::class, 'resetPassword']);
    });

    // --- Protected Routes with auth & logging ---
    Route::middleware(['auth:sanctum', 'log.api'])->group(function () {

        // Dashboard Analytics
        Route::prefix('dashboard')->group(function () {
            Route::get('summary', [DashboardController::class, 'summary']);
            Route::get('sales-performance', [DashboardController::class, 'salesPerformance']);
            Route::get('inventory-status', [DashboardController::class, 'inventoryStatus']);
            Route::get('top-products', [DashboardController::class, 'topProducts']);
        });

        // Products
        Route::apiResource('products', ProductController::class)->except(['create', 'edit']);

        Route::prefix('products')->group(function () {
            Route::get('/{product}/stock', [ProductController::class, 'stock']);
            Route::post('/{product}/reserve', [ProductController::class, 'reserveStock']);
            Route::post('/{product}/release', [ProductController::class, 'releaseStock']);
            Route::get('/low-stock', [ProductController::class, 'lowStock']);
        });

        // Orders
        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'index']);
            Route::get('{id}', [OrderController::class, 'show']);
            Route::post('/', [OrderController::class, 'store'])->middleware('check.credit');
            Route::put('{id}/status', [OrderController::class, 'updateStatus']);
            Route::get('{id}/invoice', [OrderController::class, 'invoice']);
            Route::post('/calculate-total', [OrderController::class, 'calculateTotal']);
        });

    });
    //Customers
 

Route::prefix('v1')->middleware(['auth:sanctum', 'log.api'])->group(function () {
    Route::prefix('customers')->middleware('throttle:customer-api')->group(function () {
        Route::get('/', [CustomerController::class, 'index']);
        Route::get('/map-data', [CustomerController::class, 'mapData']);
        Route::get('{id}', [CustomerController::class, 'show']);
        Route::post('/', [CustomerController::class, 'store']);
        Route::put('{id}', [CustomerController::class, 'update']);
        Route::delete('{id}', [CustomerController::class, 'destroy']);
        Route::get('{id}/orders', [CustomerController::class, 'orderHistory']);
        Route::get('{id}/credit-status', [CustomerController::class, 'creditStatus']);
    });
});



   

    Route::prefix('v1')->middleware(['auth:sanctum', 'log.api'])->group(function () {
         Route::get('warehouses', [WarehouseController::class, 'index']);
         Route::get('warehouses/{id}/inventory', [WarehouseController::class, 'inventory']);
});

});





