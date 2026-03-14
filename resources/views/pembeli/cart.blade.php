<x-app-layout>
    <div class="py-5" style="background-color: #EDDCC6; min-height: 100vh;">
        <div class="container">
            <h2 class="fw-bold mb-4" style="color: #BF4646;">Keranjang Belanja Anda</h2>

            <div class="row">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm rounded-4 p-3">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $total = 0 @endphp
                                @if (session('cart'))
                                    @foreach (session('cart') as $id => $details)
                                        @php $total += $details['price'] * $details['quantity'] @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ asset('storage/barangs/' . $details['photo']) }}"
                                                        width="50" class="rounded-3 me-3">
                                                    <div>
                                                        <div class="fw-bold">{{ $details['name'] }}</div>
                                                        <small class="text-muted">{{ $details['merk'] }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>Rp{{ number_format($details['price'], 0, ',', '.') }}</td>
                                            <td>{{ $details['quantity'] }}</td>
                                            <td class="fw-bold">
                                                Rp{{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center py-4">Keranjang masih kosong.</td>
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
                        <button class="btn btn-light w-100 fw-bold py-2" style="color: #BF4646; border-radius: 10px;">
                            Checkout Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
