
<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MenuController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransaksiController;
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
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    // Menampilkan daftar menu
    Route::get('/menu', [MenuController::class, 'index'])
        ->name('menu.index');

    // Menambahkan menu baru
    Route::post('/menu', [MenuController::class, 'store'])
        ->name('menu.store');

    // Memperbarui menu
    Route::put('/menu/{menu}', [MenuController::class, 'update'])
        ->name('menu.update');

    Route::delete('/menu/{menu}', [MenuController::class, 'destroy'])
    ->name('menu.destroy');

    Route::patch('/menu/{menu}/status', [MenuController::class, 'toggleStatus'])
    ->name('menu.toggleStatus');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
    
    // Membuat transaksi baru
    Route::post('/transaksi', [TransaksiController::class, 'store'])
        ->name('transaksi.store');
});

require __DIR__.'/auth.php';
