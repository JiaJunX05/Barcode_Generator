<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\GuestController;
use App\Http\Middleware\AdminAuth;

// Guest Routes
Route::get('/', [GuestController::class, 'index'])->name('dashboard');
Route::get('/view/{id}', [GuestController::class, 'view'])->name('view');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login');

// Admin Routes
Route::prefix('admin')->group(function() {
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.submit');

    Route::middleware([AdminAuth::class])->group(function() {
        Route::get('/dashboard', [AuthController::class, 'index'])->name('admin.dashboard');
        Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

        Route::get('/product/list', [ProductController::class, 'index'])->name('admin.dashboard');
        Route::get('/product/create', [ProductController::class, 'showCreateForm'])->name('product.create');
        Route::post('/product/create', [ProductController::class, 'create'])->name('product.create.submit');
        Route::get('/product/view/{id}', [ProductController::class, 'view'])->name('product.view');
        Route::get('/product/update/{id}', [ProductController::class, 'showUpdateForm'])->name('product.update');
        Route::put('/product/update/{id}', [ProductController::class, 'update'])->name('product.update.submit');
        Route::get('/product/upload/{id}', [ProductController::class, 'showUploadForm'])->name('product.upload');
        Route::put('/product/upload/{id}', [ProductController::class, 'upload'])->name('product.upload.submit');
        Route::delete('/product/delete/{id}', [ProductController::class, 'destroy'])->name('product.destroy');
    });
});
