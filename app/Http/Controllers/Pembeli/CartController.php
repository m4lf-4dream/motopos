<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $barang = Barang::findOrFail($request->barang_id);
        $qty_requested = $request->quantity;

        // Ambil data baru dari pop-up modal
        $metode_requested = $request->metode;
        $payment_requested = $request->payment;

        // Validasi stok
        if ($qty_requested > $barang->stok) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi!');
        }

        $cart = session()->get('cart', []);

        if(isset($cart[$barang->id])) {
            $new_qty = $cart[$barang->id]['quantity'] + $qty_requested;

            if ($new_qty > $barang->stok) {
                return redirect()->back()->with('error', 'Total di keranjang melebihi stok!');
            }

            $cart[$barang->id]['quantity'] = $new_qty;
            $cart[$barang->id]['metode'] = $metode_requested;
            $cart[$barang->id]['payment'] = $payment_requested;
        } else {
            $cart[$barang->id] = [
                "name" => $barang->nama_barang,
                "quantity" => $qty_requested,
                "price" => $barang->harga,
                "photo" => $barang->foto,
                "merk" => $barang->merk,
                "metode" => $metode_requested,
                "payment" => $payment_requested // Data pembayaran masuk ke sini
            ];
        }

        session()->put('cart', $cart);
        return redirect()->route('cart.index')->with('success', 'Barang berhasil masuk keranjang!');
    }

    public function showCart()
    {
        return view('pembeli.cart');
    }
}
