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

// DASHBOARD
Route::get('/dashboard', function () {
    $role = Auth::user()->role;
    $barangs = Barang::where('stok', '>', 0)->get();

    if ($role == 'kasir') {
        return view('dashboard.kasir');
    }

    return view('dashboard.pembeli', compact('barangs'));
})->middleware(['auth', 'verified'])->name('dashboard');

// GRUP AUTH (PROFILE & PEMBELI)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // FITUR KERANJANG & PESANAN PEMBELI
    Route::prefix('cart')->group(function () {
        // Halaman Utama Keranjang
        Route::get('/', [CartController::class, 'showCart'])->name('pembeli.cart');

        // Logika Checkout (Sudah disesuaikan dengan JavaScript di Blade)
        Route::post('/checkout-cash', [CartController::class, 'checkoutCash'])->name('cart.checkout.cash');
        Route::post('/checkout-midtrans', [CartController::class, 'checkoutMidtrans'])->name('cart.checkout.midtrans');

        // Riwayat Pesanan
        Route::get('/pesanan-anda', [CartController::class, 'history'])->name('pembeli.pesanan');
    });
});

// GRUP KASIR
Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('kasir')->group(function () {
        // CRUD Barang
        Route::get('/barang', [BarangController::class, 'index'])->name('kasir.crud');
        Route::post('/barang', [BarangController::class, 'store'])->name('kasir.barang.store');
        Route::put('/barang/{id}', [BarangController::class, 'update'])->name('kasir.barang.update');
        Route::delete('/barang/{id}', [BarangController::class, 'destroy'])->name('kasir.barang.destroy');

        // Transaksi & Antrean
        Route::get('/transaksi', [TransaksiController::class, 'create'])->name('kasir.transaksi');
        Route::get('/antrean', [TransaksiController::class, 'getAntrean'])->name('kasir.antrean');
        Route::post('/transaksi/store', [TransaksiController::class, 'store'])->name('kasir.transaksi.store');
        Route::get('/riwayat', [TransaksiController::class, 'index'])->name('kasir.riwayat');
        Route::post('/transaksi/konfirmasi/{id}', [TransaksiController::class, 'konfirmasi'])->name('kasir.konfirmasi');
    });
});

// MIDTRANS CALLBACK (Luar Middleware Auth agar Midtrans bisa akses)
Route::post('/midtrans-callback', [CartController::class, 'callback'])->name('midtrans.callback');

require __DIR__ . '/auth.php';   
