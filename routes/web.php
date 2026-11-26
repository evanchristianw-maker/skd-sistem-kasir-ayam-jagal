<?php

use App\Http\Controllers\MenuController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\LaporanPenjualanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RekeningBankController;
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

    // Manajemen Rekening Bank
    Route::get('/rekening-bank', [RekeningBankController::class, 'index'])
        ->name('rekening-bank.index');

    Route::post('/rekening-bank', [RekeningBankController::class, 'store'])
        ->name('rekening-bank.store');

    Route::put('/rekening-bank/{rekeningBank}', [RekeningBankController::class, 'update'])
        ->name('rekening-bank.update');

    Route::delete('/rekening-bank/{rekeningBank}', [RekeningBankController::class, 'destroy'])
        ->name('rekening-bank.destroy');

    Route::patch('/rekening-bank/{rekeningBank}/status', [RekeningBankController::class, 'toggleStatus'])
        ->name('rekening-bank.toggleStatus');
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

Route::middleware(['auth', 'role:admin,kasir'])->group(function () {
    // Konfirmasi pembayaran transfer
    Route::post('/transaksi/{transaksi}/konfirmasi', [TransaksiController::class, 'konfirmasi'])
        ->name('transaksi.konfirmasi');

    // Pembatalan pembayaran transfer
    Route::post('/transaksi/{transaksi}/batalkan', [TransaksiController::class, 'batalkan'])
        ->name('transaksi.batalkan');
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