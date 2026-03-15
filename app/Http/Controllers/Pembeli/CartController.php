<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function __construct()
    {
        // Konfigurasi Midtrans dari .env
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

        // Validasi stok
        if ($qty > $barang->stok) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi!');
        }

        $cart = session()->get('cart', []);

        // Data dasar barang untuk session
        $cartData = [
            "name" => $barang->nama_barang,
            "quantity" => $qty,
            "price" => $barang->harga,
            "photo" => $barang->foto,
            "merk" => $barang->merk,
            "metode" => $request->metode ?? 'Ambil Sendiri', // Tambahkan default
            "payment" => $request->payment ?? 'Cash',       // Tambahkan default
            "snap_token" => null
        ];

        // LOGIKA MIDTRANS JIKA PILIH E-MONEY
        if ($request->payment == 'E-Money') {
            $params = [
                'transaction_details' => [
                    'order_id' => 'MOTOPART-' . uniqid(),
                    'gross_amount' => (int)$total_harga,
                ],
                'customer_details' => [
                    // Cek jika user login, gunakan nama. Jika tidak, pakai Guest.
                    'first_name' => Auth::check() ? (Auth::user()->name ?? Auth::user()->nama) : 'Guest',
                    'email' => Auth::check() ? Auth::user()->email : 'guest@motopart.com',
                ],
            ];

            try {
                $snapToken = Snap::getSnapToken($params);
                $cartData['snap_token'] = $snapToken;
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Koneksi Midtrans Gagal: ' . $e->getMessage());
            }
        }

        $cart[$barang->id] = $cartData;
        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Barang berhasil masuk keranjang!');
    }

    public function showCart()
    {
        return view('pembeli.cart');
    }

    public function remove(Request $request)
    {
        if ($request->id) {
            $cart = session()->get('cart');
            if (isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
            return redirect()->back()->with('success', 'Barang dihapus!');
        }
    }
}
