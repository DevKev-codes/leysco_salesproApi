<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Middleware\LogApiActivity;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\ProductStockController;
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

Route::prefix('v1')->middleware(['auth:sanctum', LogApiActivity::class])->group(function () {
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']); // List with filters/search
        Route::post('/', [ProductController::class, 'store'])->middleware('can:admin-only');
        Route::get('{id}', [ProductController::class, 'show']);
        Route::put('{id}', [ProductController::class, 'update'])->middleware('can:admin-only');
        Route::delete('{id}', [ProductController::class, 'destroy'])->middleware('can:admin-only');

        Route::get('{id}/stock', [ProductStockController::class, 'stock']);
        Route::post('{id}/reserve', [ProductStockController::class, 'reserve']);
        Route::post('{id}/release', [ProductStockController::class, 'release']);

        Route::get('low-stock', [ProductController::class, 'lowStock']);
    });
});

});

