<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // User routes
    Route::prefix('user')->group(function () {
        Route::post('/create', [UserController::class, 'create']);
        Route::post('/login', [UserController::class, 'login']);
        Route::get('/logout', [UserController::class, 'logout']);
        Route::post('/forgot-password', [UserController::class, 'passwordResetToken']);
        Route::post('/reset-password-token', [UserController::class, 'passwordReset']);

        Route::middleware('jwt')->group(function () {
            Route::get('/', [UserController::class, 'index']);
            Route::put('/edit', [UserController::class, 'edit']);
            Route::delete('/', [UserController::class, 'destroy']);
            Route::get('/orders', [UserController::class, 'orders']);
        });
    });

    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::post('create', [AdminController::class, 'create'])->name('create');
        Route::post('login', [AdminController::class, 'login'])->name('login');
        Route::get('logout', [AdminController::class, 'logout'])->name('logout');

        Route::middleware(['jwt', 'role:admin'])->group(function () {
            Route::name('admin.')->group(function () {
                Route::get('user-listing', [AdminController::class, 'index'])->name('index');
                Route::put('user-edit/{user}', [AdminController::class, 'update'])->name('update');
                Route::delete('user-delete/{user}', [AdminController::class, 'destroy'])->name('delete');
            });
        });
    });

    // Public brand routes
    Route::get('brands', [BrandController::class, 'index']);
    Route::apiResource('brand', BrandController::class)->except(['index']);

    // Category routes
    Route::get('categories', [CategoryController::class, 'index']);
    Route::apiResource('category', CategoryController::class)->except(['index']);

    // Product routes
    Route::get('products', [ProductController::class, 'index']);
    Route::apiResource('product', ProductController::class)->except(['index']);

});


