<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Barang;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    /**
     * Menampilkan semua riwayat transaksi untuk Kasir
     */
    public function index()
    {
        // Mengambil data transaksi, diurutkan dari yang terbaru (latest)
        // with('barang') digunakan agar "jembatan" relasi yang kita buat tadi langsung aktif
        $semuaTransaksi = Transaksi::with('barang')->latest()->get();

        return view('kasir.riwayat', compact('semuaTransaksi'));
    }

    /**
     * Fungsi khusus untuk Kasir mengonfirmasi pembayaran CASH
     */
    public function konfirmasi($id)
    {
        $transaksi = Transaksi::findOrFail($id);

        // Pastikan hanya status 'Pending' yang bisa dikonfirmasi
        if ($transaksi->status == 'Pending') {
            $transaksi->update([
                'status' => 'Success'
            ]);

            return redirect()->back()->with('success', 'Pembayaran Cash berhasil dikonfirmasi!');
        }

        return redirect()->back()->with('error', 'Transaksi sudah diproses sebelumnya.');
    }
}
