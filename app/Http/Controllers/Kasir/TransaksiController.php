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
    $bayar = $request->bayar;

    if (empty($items)) {
        return response()->json(['status' => 'error', 'message' => 'Keranjang kosong'], 400);
    }

    DB::beginTransaction();

    try {
        $total_belanja = 0;
        $order_id_baru = 'POS-' . strtoupper(uniqid());

        foreach ($items as $item) {
            $barang = Barang::lockForUpdate()->find($item['id']);

            if (!$barang || $barang->stok < $item['quantity']) {
                throw new \Exception("Stok {$barang->nama_barang} tidak mencukupi.");
            }

            $subtotal = $item['price'] * $item['quantity'];
            $total_belanja += $subtotal;


            Transaksi::create([
                'order_id' => $order_id_baru,
                'barang_id' => $item['id'],
                'jumlah' => $item['quantity'],
                'total_harga' => $subtotal,
                'metode_pembayaran' => 'Cash',
                'status' => 'Success',
            ]);

            $barang->decrement('stok', $item['quantity']);
        }

       
        if ($request->has('order_id') && !empty($request->order_id)) {
            Transaksi::where('order_id', $request->order_id)
                     ->where('status', 'Pending')
                     ->delete();
        }

        DB::commit();

        //STRUK
        return response()->json([
            'status' => 'success',
            'redirect' => route('kasir.riwayat'),
            'data_struk' => [
                'order_id' => $order_id_baru,
                'items' => $items,
                'total' => $total_belanja,
                'bayar' => $bayar,
                'kembali' => $bayar - $total_belanja,
                'waktu' => now()->format('d-m-Y H:i'),
                'kasir' => Auth::user()->name
            ]
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

    public function getAntrean()
    {

        $antrean = \App\Models\Transaksi::with('barang')
            ->where('status', 'Pending')
            ->where('metode_pembayaran', 'Cash')
            ->get()
            ->groupBy('order_id');

        return response()->json($antrean);
    }
}
