<x-app-layout>
    <div class="py-5" style="background-color: #EDDCC6; min-height: 100vh;">
        <div class="container">
            <div class="text-center mb-5">
                <h1 class="fw-bold" style="color: #BF4646;">Katalog Sparepart MotoPart</h1>
                <p class="text-muted">Temukan suku cadang terbaik untuk motor kesayangan Anda</p>
            </div>

            <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
                @foreach($barangs as $item)
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative">
                        <span class="position-absolute top-0 start-0 m-3 badge rounded-pill px-3 py-2" style="background-color: #BF4646; z-index: 10;">
                            {{ $item->merk }}
                        </span>

                        <div style="height: 200px; overflow: hidden;">
                            <img src="{{ asset('storage/barangs/' . $item->foto) }}" class="card-img-top w-100 h-100" style="object-fit: cover;" alt="{{ $item->nama_barang }}">
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold text-dark mb-1">{{ $item->nama_barang }}</h5>
                            <p class="text-muted small mb-3">Warna: {{ $item->warna }}</p>

                            <div class="mt-auto">
                                <h5 class="fw-bold mb-3" style="color: #BF4646;">Rp{{ number_format($item->harga, 0, ',', '.') }}</h5>

                                <form action="#" method="POST"> @csrf
                                    <input type="hidden" name="barang_id" value="{{ $item->id }}">
                                    <button type="submit" class="btn w-100 text-white fw-bold shadow-sm" style="background-color: #BF4646; border-radius: 10px;">
                                        <i class="bi bi-cart-plus"></i> + Keranjang
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
