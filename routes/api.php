<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\MenuItemController;
use App\Http\Controllers\Api\V1\ModifierController;
use App\Http\Controllers\Api\V1\TableController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
    });

    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{category}', [CategoryController::class, 'show']);
    Route::get('menu-items', [MenuItemController::class, 'index']);
    Route::get('menu-items/{menuItem}', [MenuItemController::class, 'show']);
    Route::get('menu-items/{menuItem}/modifiers', [ModifierController::class, 'index']);

    Route::middleware('auth:sanctum')->group(function () {

        Route::prefix('auth')->group(function () {
            Route::get('me', [AuthController::class, 'me']);
            Route::post('logout', [AuthController::class, 'logout']);
        });

        Route::middleware('role:admin,manager')->group(function () {
            Route::post('categories', [CategoryController::class, 'store']);
            Route::put('categories/{category}', [CategoryController::class, 'update']);
            Route::delete('categories/{category}', [CategoryController::class, 'destroy']);
        });

        Route::middleware('role:admin,manager')->group(function () {
            Route::post('menu-items', [MenuItemController::class, 'store']);
            Route::put('menu-items/{menuItem}', [MenuItemController::class, 'update']);
            Route::delete('menu-items/{menuItem}', [MenuItemController::class, 'destroy']);
        });

        Route::middleware('role:admin,manager')->group(function () {
            Route::post('menu-items/{menuItem}/modifiers', [ModifierController::class, 'store']);
            Route::put('menu-items/{menuItem}/modifiers/{modifier}', [ModifierController::class, 'update']);
            Route::delete('menu-items/{menuItem}/modifiers/{modifier}', [ModifierController::class, 'destroy']);
        });

        Route::get('tables', [TableController::class, 'index']);
        Route::get('tables/{table}', [TableController::class, 'show']);
        Route::middleware('role:admin,manager')->group(function () {
            Route::post('tables', [TableController::class, 'store']);
            Route::put('tables/{table}', [TableController::class, 'update']);
            Route::delete('tables/{table}', [TableController::class, 'destroy']);
        });

    });

});
