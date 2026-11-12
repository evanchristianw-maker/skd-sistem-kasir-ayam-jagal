<?php

use App\Http\Controllers\MenuController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\LaporanPenjualanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\NotificationController;
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

// ==========================================
// DASHBOARD BERDASARKAN ROLE
// ==========================================

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->role === 'admin') {
        return app(\App\Http\Controllers\DashboardController::class)->index();
    }

    if ($user->role === 'kasir') {
        return redirect()->route('cashier.transactions.index');
    }

    abort(403, 'Role pengguna tidak dikenali.');
})->middleware('auth')->name('dashboard');


// ==========================================
// KHUSUS ADMIN
// ==========================================

Route::middleware(['auth', 'role:admin'])->group(function () {

    // Manajemen Menu
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

    // Laporan Penjualan
    Route::get('/sales', [LaporanPenjualanController::class, 'index'])
        ->name('sales.index');

    // Laporan Keuangan
    Route::get('/finance', [FinanceController::class, 'index'])
        ->name('finance.index');
});


// ==========================================
// KHUSUS KASIR
// ==========================================

Route::middleware(['auth', 'role:kasir'])->group(function () {

    // Halaman Transaksi Kasir
    Route::get('/cashier/transaksi', [MenuController::class, 'cashierIndex'])
    ->name('cashier.transactions.index');

    // Menyimpan Transaksi
    Route::post('/transaksi', [TransaksiController::class, 'store'])
        ->name('transaksi.store');
});
Route::middleware(['auth', 'role:admin,kasir'])
    ->prefix('notifications')
    ->name('notifications.')
    ->group(function () {
        Route::get('/', [NotificationController::class, 'index'])
            ->name('index');

        Route::patch('/{id}/read', [NotificationController::class, 'markAsRead'])
            ->name('read');

        Route::delete('/{id}', [NotificationController::class, 'destroy'])
            ->name('destroy');
    });

require __DIR__.'/auth.php';