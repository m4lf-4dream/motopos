<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index()
    {
        if (Auth::user()->role !== 'kasir') {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak!');
        }

        $semuaTransaksi = Transaksi::with('barang')->latest()->get();
        return view('kasir.riwayat', compact('semuaTransaksi'));
    }

    public function create()
    {
        if (Auth::user()->role !== 'kasir') {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak!');
        }

        $barangs = Barang::where('stok', '>', 0)->get();
        return view('kasir.transaksi', compact('barangs'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'kasir') {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $items = json_decode($request->items, true);

        if (empty($items)) {
            return response()->json(['status' => 'error', 'message' => 'Keranjang kosong'], 400);
        }

        DB::beginTransaction();

        try {
            foreach ($items as $item) {
                $barang = Barang::lockForUpdate()->find($item['id']);

                if (!$barang || $barang->stok < $item['quantity']) {
                    throw new \Exception("Stok {$barang->nama_barang} tidak mencukupi.");
                }

                Transaksi::create([
                    'order_id' => 'POS-' . strtoupper(uniqid()),
                    'barang_id' => $item['id'],
                    'jumlah' => $item['quantity'],
                    'total_harga' => $item['price'] * $item['quantity'],
                    'metode_pembayaran' => 'Cash',
                    'status' => 'Success',
                ]);

                $barang->decrement('stok', $item['quantity']);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'redirect' => route('kasir.riwayat')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function konfirmasi($id)
    {
        $transaksi = Transaksi::findOrFail($id);

        if ($transaksi->status == 'Pending') {
            $transaksi->update(['status' => 'Success']);
            return redirect()->back()->with('success', 'Pembayaran Berhasil!');
        }

        return redirect()->back()->with('error', 'Transaksi sudah selesai.');
    }
}
