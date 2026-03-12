<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BarangController extends Controller
{
    // 1. Menampilkan halaman daftar barang
    public function index()
    {
        $barangs = Barang::all(); // Mengambil semua data barang dari database
        return view('kasir.crud', compact('barangs'));
    }

    // 2. Menyimpan data barang baru
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'kode_barang' => 'required|unique:barangs',
            'nama_barang' => 'required',
            'merk'        => 'required',
            'warna'       => 'required',
            'harga'       => 'required|numeric',
            'stok'        => 'required|numeric',
            'foto'        => 'required|image|mimes:jpeg,png,jpg,webp|max:2048', // Max 2MB
        ]);

        // Proses upload foto
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $nama_file = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/barangs'), $nama_file);
        }

        // Simpan data ke database
        Barang::create([
            'kode_barang' => $request->kode_barang,
            'nama_barang' => $request->nama_barang,
            'merk'        => $request->merk,
            'warna'       => $request->warna,
            'harga'       => $request->harga,
            'stok'        => $request->stok,
            'foto'        => $nama_file,
        ]);

        return redirect()->route('kasir.crud')->with('success', 'Barang berhasil ditambahkan!');
    }
}
