<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Transaksi;
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

        $orderId = 'MOTOPART-' . uniqid();

        if ($qty > $barang->stok) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi!');
        }

        Transaksi::create([
            'order_id' => $orderId,
            'barang_id' => $barang->id,
            'jumlah' => $qty,
            'total_harga' => $total_harga,
            'metode_pembayaran' => $request->payment,
            'status' => 'Pending',
        ]);

        //Midtrans
        $snapToken = null;
        if ($request->payment == 'E-Money') {
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int)$total_harga,
                ],
                'customer_details' => [
                    'first_name' => Auth::user()->name ?? 'Guest',
                    'email' => Auth::user()->email ?? 'guest@motopart.com',
                ],
            ];

            try {
                $snapToken = Snap::getSnapToken($params);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Midtrans Gagal: ' . $e->getMessage());
            }
        }

        //Session
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
            "order_id" => $orderId
        ];

        session()->put('cart', $cart);
        $barang->decrement('stok', $qty);

        return redirect()->route('cart.index')->with('success', 'Masuk keranjang!');
    }

    public function showCart()
    {
        return view('pembeli.cart');
    }

    //CHECKOUT CASH
    public function checkoutCash()
    {
        $cart = session()->get('cart');

        if (!$cart) {
            return redirect()->route('dashboard')->with('error', 'Keranjang sudah kosong.');
        }

        $userRole = Auth::user()->role;
        session()->forget('cart');

        if ($userRole === 'kasir') {
            return redirect()->route('kasir.riwayat')->with('success', 'Transaksi berhasil dicatat!');
        }
        return redirect()->route('dashboard')->with('success', 'Pesanan dikirim! Silahkan bayar di Kasir.');
    }

    public function remove(Request $request)
    {
        if ($request->id) {
            $cart = session()->get('cart');
            if (isset($cart[$request->id])) {
                $barang = Barang::find($request->id);
                if ($barang) {
                    $barang->increment('stok', $cart[$request->id]['quantity']);
                }

                Transaksi::where('order_id', $cart[$request->id]['order_id'])->delete();

                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
            return redirect()->back()->with('success', 'Barang dibatalkan.');
        }
    }

    public function callback(Request $request)
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed == $request->signature_key) {
            if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                $transaksi = Transaksi::where('order_id', $request->order_id)->first();
                if ($transaksi) {
                    $transaksi->update(['status' => 'Success']);
                }
            }
        }
        return response()->json(['status' => 'ok']);
    }
}
