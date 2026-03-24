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

//DASHBOARD
Route::get('/dashboard', function () {
    $role = Auth::user()->role;
    $barangs = Barang::where('stok', '>', 0)->get();

    if ($role == 'kasir') {
        return view('dashboard.kasir');
    }

    return view('dashboard.pembeli', compact('barangs'));
})->middleware(['auth', 'verified'])->name('dashboard');

//ROUTE KHUSUS PEMBELI
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //Fitur Keranjang
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'showCart'])->name('cart.index');
        Route::post('/add', [CartController::class, 'addToCart'])->name('cart.add');
        Route::delete('/remove', [CartController::class, 'remove'])->name('cart.remove');
    });
});

//KASIR
Route::middleware(['auth', 'verified'])->group(function () {

    //CRUD
    Route::prefix('kasir/barang')->group(function () {
        Route::get('/', [BarangController::class, 'index'])->name('kasir.crud');
        Route::post('/', [BarangController::class, 'store'])->name('kasir.barang.store');
        Route::put('/{id}', [BarangController::class, 'update'])->name('kasir.barang.update');
        Route::delete('/{id}', [BarangController::class, 'destroy'])->name('kasir.barang.destroy');
    });

    //Kasir
    Route::get('/kasir/transaksi', [TransaksiController::class, 'create'])->name('kasir.transaksi');

    //Riwayuy
    Route::get('/kasir/riwayat', [TransaksiController::class, 'index'])->name('kasir.riwayat');

    //Confirm Pay
    Route::post('/kasir/konfirmasi/{id}', [TransaksiController::class, 'konfirmasi'])->name('kasir.konfirmasi');

    //cartbaruuw
    Route::post('/cart/checkout-cash', [CartController::class, 'checkoutCash'])->name('cart.checkout.cash');
});

//MIDTRANS
Route::post('/midtrans-callback', [CartController::class, 'callback'])->name('midtrans.callback');

require __DIR__ . '/auth.php';
