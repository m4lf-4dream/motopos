<?php

use App\Http\Controllers\Kasir\TransaksiController;
use App\Http\Controllers\Pembeli\CartController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Kasir\BarangController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Barang;

Route::get('/', function () {
    return view('welcome');
});

// ROUTE DASHBOARD
Route::get('/dashboard', function () {
    $role = Auth::user()->role;
    $barangs = Barang::where('stok', '>', 0)->get();

    if ($role == 'kasir'){
        return view('dashboard.kasir');
    }

    return view('dashboard.pembeli', compact('barangs'));
})->middleware(['auth', 'verified'])->name('dashboard');

// ROUTE PEMBELI (Profile & Keranjang)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Fitur Keranjang Terpadu
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'showCart'])->name('cart.index');
        Route::post('/add', [CartController::class, 'addToCart'])->name('cart.add');
        Route::delete('/remove', [CartController::class, 'remove'])->name('cart.remove');
    });
});

// ROUTE KASIR
Route::middleware(['auth', 'verified'])->group(function () {
    // CRUD Barang
    Route::get('/kasir/barang', [BarangController::class, 'index'])->name('kasir.crud');
    Route::post('/kasir/barang', [BarangController::class, 'store'])->name('kasir.barang.store');
    Route::put('/kasir/barang/{id}', [BarangController::class, 'update'])->name('kasir.barang.update');
    Route::delete('/kasir/barang/{id}', [BarangController::class, 'destroy'])->name('kasir.barang.destroy');

    // Transaksi & Riwayat (DIUBAH KE CONTROLLER)
    Route::get('/kasir/transaksi', [TransaksiController::class, 'index'])->name('kasir.transaksi'); // Bisa pakai index yang sama atau beda
    Route::get('/kasir/riwayat', [TransaksiController::class, 'index'])->name('kasir.riwayat');

    // Route untuk Tombol Konfirmasi Cash
    Route::post('/kasir/transaksi/konfirmasi/{id}', [TransaksiController::class, 'konfirmasi'])->name('kasir.konfirmasi');
});

// Callback Midtrans (Harus POST dan di luar middleware auth)
Route::post('/midtrans-callback', [CartController::class, 'callback'])->name('midtrans.callback');

require __DIR__.'/auth.php';
