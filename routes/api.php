<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\GatewayController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Authentication
Route::post('/login', [AuthController::class, 'login']);

// Payment (public)
Route::post('/payments', [PaymentController::class, 'store']);

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Gateways (ADMIN only)
    Route::middleware(['permission:manage-gateways'])->group(function () {
        Route::get('/gateways', [GatewayController::class, 'index']);
        Route::get('/gateways/{gateway}', [GatewayController::class, 'show']);
        Route::patch('/gateways/{gateway}/status', [GatewayController::class, 'updateStatus']);
        Route::patch('/gateways/{gateway}/priority', [GatewayController::class, 'updatePriority']);
    });

    // Users (ADMIN, MANAGER)
    Route::middleware(['permission:manage-users'])->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
    });

    // Products (ADMIN, MANAGER, FINANCE)
    Route::middleware(['permission:manage-products'])->group(function () {
        Route::get('/products', [ProductController::class, 'index']);
        Route::post('/products', [ProductController::class, 'store']);
        Route::get('/products/{product}', [ProductController::class, 'show']);
        Route::put('/products/{product}', [ProductController::class, 'update']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    });

    // Clients (all authenticated users)
    Route::get('/clients', [ClientController::class, 'index']);
    Route::get('/clients/{client}', [ClientController::class, 'show']);

    // Transactions (all authenticated users)
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show']);

    // Refunds (ADMIN, FINANCE)
    Route::middleware(['permission:process-refunds'])->group(function () {
        Route::post('/transactions/{transaction}/refund', [TransactionController::class, 'refund']);
    });
});

