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

    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);

        if ($barang->foto && file_exists(public_path('storage/barangs/' . $barang->foto))) {
        unlink(public_path('storage/barangs/' . $barang->foto));
    }

        $barang->delete();
        return redirect()->back()->with('success', 'Barang berhasil dihapus!');
    }

    public function update(Request $request, $id)
{
    $barang = Barang::findOrFail($id);

    $request->validate([
        'nama_barang' => 'required',
        'harga'       => 'required|numeric',
        'stok'        => 'required|numeric',
        'foto'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
    ]);

    if ($request->hasFile('foto')) {
        if ($barang->foto && file_exists(public_path('storage/barangs/' . $barang->foto))) {
            unlink(public_path('storage/barangs/' . $barang->foto));
        }

        $file = $request->file('foto');
        $nama_file = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('storage/barangs'), $nama_file);
        $barang->foto = $nama_file;
    }

    $barang->update([
        'nama_barang' => $request->nama_barang,
        'harga'       => $request->harga,
        'stok'        => $request->stok,
    ]);

    return redirect()->back()->with('success', 'Data berhasil diperbarui!');
}
public function callback(Request $request)
{
    $serverKey = env('MIDTRANS_SERVER_KEY');
    $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

    // 1. Validasi apakah data benar-benar dari Midtrans
    if ($hashed == $request->signature_key) {

        // 2. Jika pembayaran sukses (Settlement)
        if ($request->transaction_status == 'settlement' || $request->transaction_status == 'capture') {


            return response()->json(['message' => 'Pembayaran berhasil, stok diupdate'], 200);
        }
    }

    return response()->json(['message' => 'Signature invalid'], 403);
}
}

