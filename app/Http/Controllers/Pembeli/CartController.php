<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;

class CartController extends Controller
{
    public function __construct()
    {

        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = env('MIDTRANS_IS_SANITIZED', true);
        Config::$is3ds = env('MIDTRANS_IS_3DS', true);
    }


    public function showCart()
    {
        return view('pembeli.cart');
    }


    public function checkoutCash(Request $request)
    {
        // PENTING: Karena di Blade kamu pakai JSON.stringify(cart),
        // maka di sini kita harus decode kembali agar menjadi array PHP.
        $itemsData = json_decode($request->items, true);

        if (!$itemsData || count($itemsData) == 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Keranjang belanja kosong.'
            ], 400);
        }

        $newOrderId = 'MTP-' . strtoupper(uniqid());
        $userId = Auth::id(); // Mengambil ID dari user 'alfian' yang sedang login

        // Validasi jika user tidak sengaja ter-logout
        if (!$userId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sesi login berakhir, silakan login kembali.'
            ], 401);
        }

        DB::beginTransaction();

        try {
            foreach ($itemsData as $item) {
                Transaksi::create([
                    'order_id'          => $newOrderId,
                    'user_id'           => $userId,
                    // Di JS kamu pakai 'id', maka di sini kita petakan ke 'barang_id'
                    'barang_id'         => $item['id'],
                    'jumlah'            => $item['quantity'],
                    'total_harga'       => $item['price'] * $item['quantity'],
                    'metode_pembayaran' => 'Cash',
                    'status'            => 'Pending',
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'order_id' => $newOrderId
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                // Memberikan pesan error spesifik agar "Mengirim..." berhenti dan kita tahu salahnya
                'message' => 'Gagal simpan database: ' . $e->getMessage()
            ], 500);
        }
    }

    public function history()
    {

        $orders = Transaksi::with('barang')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pembeli.pesanan', compact('orders'));
    }


    public function callback(Request $request)
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed == $request->signature_key) {
            if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                Transaksi::where('order_id', $request->order_id)->update(['status' => 'Success']);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    public function addToCart(Request $request)
    {
        $barang = Barang::findOrFail($request->barang_id);

        if ($request->quantity > $barang->stok) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi!');
        }

        $cart = session()->get('cart', []);
        $cart[$barang->id] = [
            "name"     => $barang->nama_barang,
            "quantity" => $request->quantity,
            "price"    => $barang->harga,
            "metode"   => $request->metode ?? 'Ambil Sendiri',
            "payment"  => $request->payment,
        ];

        session()->put('cart', $cart);

        return redirect()->route('pembeli.cart')->with('success', 'Berhasil ditambahkan ke keranjang!');
    }
}
