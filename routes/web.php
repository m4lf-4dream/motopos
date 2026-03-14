<?php

use App\Http\Controllers\Pembeli\CartController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Kasir\BarangController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Barang; // Import Model Barang

Route::get('/', function () {
    return view('welcome');
});

// ROUTE DASHBOARD (Hanya satu definisi saja)
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

    // Fitur Keranjang
    Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
    Route::get('/cart', [CartController::class, 'showCart'])->name('cart.index');
});

// ROUTE KASIR (CRUD Barang & Transaksi)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/kasir/barang', [BarangController::class, 'index'])->name('kasir.crud');
    Route::post('/kasir/barang', [BarangController::class, 'store'])->name('kasir.barang.store');
    Route::put('/kasir/barang/{id}', [BarangController::class, 'update'])->name('kasir.barang.update');
    Route::delete('/kasir/barang/{id}', [BarangController::class, 'destroy'])->name('kasir.barang.destroy');

    Route::get('/kasir/transaksi', function () {
        return view('kasir.transaksi');
    })->name('kasir.transaksi');

    Route::get('/kasir/riwayat', function () {
        return view('kasir.riwayat');
    })->name('kasir.riwayat');
});

require __DIR__.'/auth.php';
