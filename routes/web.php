<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Kasir\BarangController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
  $role = Auth::user()->role;
  if ($role == 'kasir'){
    return view('dashboard.kasir');
  }

return view('dashboard.pembeli');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

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
