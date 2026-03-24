<x-app-layout>
    <div class="py-5" style="background-color: #EDDCC6; min-height: 100vh;">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold" style="color: #BF4646;">Keranjang Belanja</h2>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-dark fw-bold" style="border-radius: 10px;">
                    <i class="bi bi-arrow-left"></i> Kembali Belanja
                </a>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm rounded-4 p-3 mb-3">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $total = 0 @endphp
                                @if (session('cart') && count(session('cart')) > 0)
                                    @foreach (session('cart') as $id => $details)
                                        @php $total += $details['price'] * $details['quantity'] @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ asset('storage/barangs/' . $details['photo']) }}"
                                                        width="50" height="50" class="rounded-3 me-3"
                                                        style="object-fit: cover;">
                                                    <div>
                                                        <div class="fw-bold">{{ $details['name'] }}</div>
                                                        <span class="badge bg-secondary"
                                                            style="font-size: 0.7rem;">{{ $details['metode'] }}</span>
                                                        <span class="badge bg-info text-dark"
                                                            style="font-size: 0.7rem;">{{ $details['payment'] }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>Rp{{ number_format($details['price'], 0, ',', '.') }}</td>
                                            <td>{{ $details['quantity'] }}</td>
                                            <td class="fw-bold">
                                                Rp{{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }}
                                            </td>
                                            <td>
                                                <form action="{{ route('cart.remove') }}" method="POST"
                                                    onsubmit="return confirm('Hapus barang ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="id" value="{{ $id }}">
                                                    <button type="submit"
                                                        class="btn btn-sm btn-outline-danger border-0">
                                                        <i class="bi bi-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <p class="text-muted mb-0">Keranjang masih kosong.</p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-white" style="background-color: #BF4646;">
                        <h4 class="fw-bold mb-4">Ringkasan</h4>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Total Harga:</span>
                            <span class="fw-bold fs-5">Rp{{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        <hr>

                        @if (session('cart') && count(session('cart')) > 0)
                            @php $firstItem = collect(session('cart'))->first(); @endphp

                            @if ($firstItem['payment'] == 'E-Money' && isset($firstItem['snap_token']))
                                <button id="pay-button" class="btn btn-light w-100 fw-bold py-2"
                                    style="color: #BF4646; border-radius: 10px;">
                                    BAYAR SEKARANG (MIDTRANS)
                                </button>
                            @else
                                <form action="{{ route('cart.checkout.cash') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-light w-100 fw-bold py-2"
                                        style="color: #BF4646; border-radius: 10px;">
                                        KONFIRMASI PESANAN
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}">
    </script>
    <script type="text/javascript">
        const payButton = document.getElementById('pay-button');
        if (payButton) {
            payButton.onclick = function() {
                window.snap.pay('{{ $firstItem['snap_token'] ?? '' }}', {
                    onSuccess: function(result) {
                        window.location.href = "{{ route('dashboard') }}";
                        alert("Pembayaran Sukses!");
                    },
                    onPending: function(result) {
                        alert("Menunggu pembayaran...");
                    },
                    onError: function(result) {
                        alert("Pembayaran Gagal!");
                    }
                });
            };
        }
    </script>
</x-app-layout>
