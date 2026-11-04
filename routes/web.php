<?php

use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\LaporanPenjualanController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Admin/Dashboard/Index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    // ==========================================
    // MANAJEMEN MENU
    // ==========================================

    Route::get('/menu', [MenuController::class, 'index'])
        ->name('menu.index');

    Route::post('/menu', [MenuController::class, 'store'])
        ->name('menu.store');

    Route::put('/menu/{menu}', [MenuController::class, 'update'])
        ->name('menu.update');

    Route::delete('/menu/{menu}', [MenuController::class, 'destroy'])
        ->name('menu.destroy');

    Route::patch('/menu/{menu}/status', [MenuController::class, 'toggleStatus'])
        ->name('menu.toggleStatus');


    // ==========================================
    // TRANSAKSI
    // ==========================================

    Route::post('/transaksi', [TransaksiController::class, 'store'])
        ->name('transaksi.store');


    // ==========================================
    // LAPORAN PENJUALAN
    // ==========================================

    Route::get('/sales', [LaporanPenjualanController::class, 'index'])
    ->name('sales.index');


    // ==========================================
    // LAPORAN KEUANGAN
    // ==========================================

    Route::get('/finance', function () {
        return Inertia::render('Admin/Finance/Index');
    })->name('finance.index');


    // ==========================================
    // PROFILE
    // ==========================================

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__.'/auth.php';