<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\MenuItemController;
use App\Http\Controllers\Api\V1\ModifierController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\TableController;
use App\Http\Controllers\Api\V1\UploadController;
use App\Http\Controllers\Api\V1\RestaurantFeedbackController;
use App\Http\Controllers\Api\V1\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Public auth routes
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
    });

    // Public menu routes
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{category}', [CategoryController::class, 'show']);
    Route::get('menu-items', [MenuItemController::class, 'index']);
    Route::get('menu-items/{menuItem}', [MenuItemController::class, 'show']);
    Route::get('menu-items/{menuItem}/modifiers', [ModifierController::class, 'index']);
    Route::post('qr-tables/session', [TableController::class, 'startGuestSession']);
    Route::post('qr-tables/finish', [TableController::class, 'finishGuestSession']);
    Route::get('tables/qr/{qrCode}', [TableController::class, 'showByQrCode']);
    Route::post('qr-orders', [OrderController::class, 'storeFromQr'])->middleware('throttle:qr-orders');
    Route::post('qr-orders/status', [OrderController::class, 'showManyFromQr']);
    Route::post('qr-tables/request-bill', [TableController::class, 'requestBillFromQr']);
    Route::post('qr-orders/{order}/feedback', [RestaurantFeedbackController::class, 'storeFromQr']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::prefix('auth')->group(function () {
            Route::get('me', [AuthController::class, 'me']);
            Route::post('logout', [AuthController::class, 'logout']);
        });

        // Users — admin only
        Route::middleware('role:admin,manager')->group(function () {
            Route::get('dashboard', [DashboardController::class, 'index']);
            Route::get('feedback', [RestaurantFeedbackController::class, 'index']);
        });

        Route::middleware('role:admin')->group(function () {
            Route::get('users', [UserController::class, 'index']);
            Route::post('users', [UserController::class, 'store']);
            Route::get('users/{user}', [UserController::class, 'show']);
            Route::put('users/{user}', [UserController::class, 'update']);
            Route::delete('users/{user}', [UserController::class, 'destroy']);
            Route::patch('users/{user}/toggle-active', [UserController::class, 'toggleActive']);
        });

        Route::post('upload/avatar', [UploadController::class, 'avatar']);
        Route::delete('upload/avatar', [UploadController::class, 'removeAvatar']);

        // Categories — admin/manager only
        Route::middleware('role:admin,manager')->group(function () {
            Route::post('categories', [CategoryController::class, 'store']);
            Route::put('categories/{category}', [CategoryController::class, 'update']);
            Route::delete('categories/{category}', [CategoryController::class, 'destroy']);
        });

        // Menu items — admin/manager only
        Route::middleware('role:admin,manager')->group(function () {
            Route::post('menu-items', [MenuItemController::class, 'store']);
            Route::put('menu-items/{menuItem}', [MenuItemController::class, 'update']);
            Route::delete('menu-items/{menuItem}', [MenuItemController::class, 'destroy']);
        });

        // Modifiers — admin/manager only
        Route::middleware('role:admin,manager')->group(function () {
            Route::post('menu-items/{menuItem}/modifiers', [ModifierController::class, 'store']);
            Route::put('menu-items/{menuItem}/modifiers/{modifier}', [ModifierController::class, 'update']);
            Route::delete('menu-items/{menuItem}/modifiers/{modifier}', [ModifierController::class, 'destroy']);
        });

        // Tables
        Route::get('tables', [TableController::class, 'index']);
        Route::get('tables/{table}', [TableController::class, 'show']);
        Route::middleware('role:admin,manager,waiter')->group(function () {
            Route::put('tables/{table}', [TableController::class, 'update']);
        });
        Route::middleware('role:admin,manager')->group(function () {
            Route::post('tables', [TableController::class, 'store']);
            Route::delete('tables/{table}', [TableController::class, 'destroy']);
        });

        // Orders
        Route::get('orders', [OrderController::class, 'index']);
        Route::post('orders', [OrderController::class, 'store']);
        Route::get('orders/{order}', [OrderController::class, 'show']);
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus']);
        Route::post('orders/{order}/items', [OrderController::class, 'addItem']);
        Route::delete('orders/{order}/items/{item}', [OrderController::class, 'removeItem']);

        // Payments — cashier/admin/manager only
        Route::middleware('role:admin,manager,cashier,waiter')->group(function () {
            Route::post('orders/{order}/pay', [PaymentController::class, 'store']);
            Route::post('tables/{table}/pay', [PaymentController::class, 'storeForTable']);
        });

    });

});
