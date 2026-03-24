<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Transaksi; // TAMBAHKAN INI
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = env('MIDTRANS_IS_SANITIZED', true);
        Config::$is3ds = env('MIDTRANS_IS_3DS', true);
    }

    public function addToCart(Request $request)
    {
        $barang = Barang::findOrFail($request->barang_id);
        $qty = $request->quantity;
        $total_harga = $barang->harga * $qty;

        // Buat ID pesanan di awal agar sama antara Database dan Midtrans
        $orderId = 'MOTOPART-' . uniqid();

        if ($qty > $barang->stok) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi!');
        }

        // --- BAGIAN 1: SIMPAN KE DATABASE ---
        $transaksi = Transaksi::create([
            'order_id' => $orderId,
            'barang_id' => $barang->id,
            'jumlah' => $qty,
            'total_harga' => $total_harga,
            'metode_pembayaran' => $request->payment,
            'status' => 'Pending', // Default selalu pending
        ]);

        // --- BAGIAN 2: LOGIKA MIDTRANS ---
        $snapToken = null;
        if ($request->payment == 'E-Money') {
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId, // Pakai ID yang sama dengan database
                    'gross_amount' => (int)$total_harga,
                ],
                'customer_details' => [
                    'first_name' => Auth::check() ? (Auth::user()->name ?? Auth::user()->nama) : 'Guest',
                    'email' => Auth::check() ? Auth::user()->email : 'guest@motopart.com',
                ],
            ];

            try {
                $snapToken = Snap::getSnapToken($params);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Midtrans Gagal: ' . $e->getMessage());
            }
        }

        // --- BAGIAN 3: SESSION & STOK ---
        $cart = session()->get('cart', []);
        $cart[$barang->id] = [
            "name" => $barang->nama_barang,
            "quantity" => $qty,
            "price" => $barang->harga,
            "photo" => $barang->foto,
            "merk" => $barang->merk,
            "metode" => $request->metode ?? 'Ambil Sendiri',
            "payment" => $request->payment,
            "snap_token" => $snapToken,
            "order_id" => $orderId // Simpan order_id di session juga
        ];

        session()->put('cart', $cart);

        // Jika Cash, stok langsung berkurang (karena sudah pasti diambil di toko)
        // Jika E-Money, stok juga berkurang agar barang "terpesan"
        $barang->decrement('stok', $qty);

        return redirect()->route('cart.index')->with('success', 'Pesanan dicatat! Silahkan cek keranjang.');
    }

    // Fungsi showCart dan remove tetap sama seperti sebelumnya
    public function showCart() { return view('pembeli.cart'); }

    public function remove(Request $request)
    {
        if ($request->id) {
            $cart = session()->get('cart');
            if (isset($cart[$request->id])) {
                $barang = Barang::find($request->id);
                if($barang) {
                    $barang->increment('stok', $cart[$request->id]['quantity']);
                }

                // Hapus juga data di tabel transaksis karena pesanan dibatalkan/dihapus
                Transaksi::where('order_id', $cart[$request->id]['order_id'])->delete();

                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
            return redirect()->back()->with('success', 'Barang dihapus, stok kembali, dan data database dibersihkan!');
        }
    }
    public function callback(Request $request)
{
    $serverKey = env('MIDTRANS_SERVER_KEY');
    $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

    if ($hashed == $request->signature_key) {
        if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
         
            $transaksi = \App\Models\Transaksi::where('order_id', $request->order_id)->first();
            if ($transaksi) {
                $transaksi->update(['status' => 'Success']);
            }
        }
    }

    return response()->json(['status' => 'ok']);
}
}
