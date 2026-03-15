<x-app-layout>
    <div class="py-5" style="background-color: #EDDCC6; min-height: 100vh;">
        <div class="container">
            <div class="text-center mb-5">
                <h1 class="fw-bold" style="color: #BF4646;">Katalog Sparepart MotoPart</h1>
                <p class="text-muted">Pilih produk dan tentukan metode pengambilan serta pembayaran</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-8">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4"
                            role="alert" style="border-radius: 12px;">
                            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4"
                            role="alert" style="border-radius: 12px;">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                </div>
            </div>

            <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
                @foreach ($barangs as $item)
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative">
                            <span class="position-absolute top-0 start-0 m-3 badge rounded-pill px-3 py-2"
                                style="background-color: #BF4646; z-index: 10;">
                                {{ $item->merk }}
                            </span>

                            <div style="height: 200px; overflow: hidden;">
                                <img src="{{ asset('storage/barangs/' . $item->foto) }}"
                                    class="card-img-top w-100 h-100" style="object-fit: cover;"
                                    alt="{{ $item->nama_barang }}">
                            </div>

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fw-bold text-dark mb-1">{{ $item->nama_barang }}</h5>
                                <p class="text-muted small mb-3">Warna: {{ $item->warna }}</p>

                                <div class="mt-auto">
                                    <h5 class="fw-bold mb-3" style="color: #BF4646;">
                                        Rp{{ number_format($item->harga, 0, ',', '.') }}</h5>

                                    <button type="button" class="btn w-100 text-white fw-bold shadow-sm"
                                        style="background-color: #BF4646; border-radius: 10px;" data-bs-toggle="modal"
                                        data-bs-target="#modalBeli{{ $item->id }}">
                                        <i class="bi bi-cart-plus"></i> + Keranjang
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="modalBeli{{ $item->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow" style="border-radius: 20px;">
                                <div class="modal-header border-0">
                                    <h5 class="modal-title fw-bold">Detail Pesanan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('cart.add') }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <input type="hidden" name="barang_id" value="{{ $item->id }}">

                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Jumlah Beli</label>
                                            <input type="number" name="quantity" class="form-control border-0 bg-light"
                                                value="1" min="1" max="{{ $item->stok }}" required>
                                            <small class="text-muted">Stok tersedia: {{ $item->stok }} pcs</small>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Metode Pengambilan</label>
                                            <select name="metode" class="form-select border-0 bg-light" required>
                                                <option value="Ambil Sendiri">Ambil Sendiri</option>
                                                <option value="Delivery">Delivery (Antar)</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Metode Pembayaran</label>
                                            <div class="d-flex gap-2">
                                                <input type="radio" class="btn-check" name="payment"
                                                    id="cash{{ $item->id }}" value="Cash" checked
                                                    autocomplete="off">
                                                <label class="btn btn-outline-danger w-50 fw-bold"
                                                    for="cash{{ $item->id }}"
                                                    style="border-radius: 10px;">Cash</label>

                                                <input type="radio" class="btn-check" name="payment"
                                                    id="emoney{{ $item->id }}" value="E-Money" autocomplete="off">
                                                <label class="btn btn-outline-danger w-50 fw-bold"
                                                    for="emoney{{ $item->id }}"
                                                    style="border-radius: 10px;">E-Money</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0">
                                        <button type="submit" class="btn w-100 text-white fw-bold py-3 shadow"
                                            style="background-color: #BF4646; border-radius: 15px;">
                                            Konfirmasi & Masuk Keranjang
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
