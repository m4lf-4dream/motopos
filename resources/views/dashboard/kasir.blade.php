<x-app-layout>

    <div class="container-fluid py-5" style="background-color: #EDDCC6; min-height: 100vh;">


        <div class="container py-5">
            <div class="row g-4 justify-content-center align-items-center">

                <div class="col-6 col-md-4 col-lg-3 text-center">
                    <a href="{{ route('kasir.transaksi') }}" class="text-decoration-none transition-hover d-block">
                        <div class=" rounded-3 d-flex align-items-center justify-content-center mx-auto mb-3 shadow-lg"
                            style="width: 160px; height: 160px;">
                            <img src="{{ asset('images/transaksi.png') }}" alt="Transaksi" class="img-fluid p-4">
                        </div>
                        <h4 class="fw-bold" style="color:#bf4646;">Transaksi</h4>
                        <p style="color: #a44040">Klik untuk ke<br> menu kasir</p>
                    </a>
                </div>

                <div class="col-6 col-md-4 col-lg-3 text-center">
                    <a href="{{ route('kasir.crud') }}" class="text-decoration-none transition-hover d-block">
                        <div class="rounded-3 d-flex align-items-center justify-content-center mx-auto mb-3 shadow-lg"
                            style="width: 160px; height: 160px;">
                            <img src="{{ asset('images/barang.png') }}" alt="Barang" class="img-fluid p-4">
                        </div>
                        <h4 class="fw-bold" style="color:#bf4646;">Barang</h4>
                        <p style="color: #a44040">Klik untuk CRUD <br> Stok Barang</p>
                    </a>
                </div>

                <div class="col-6 col-md-4 col-lg-3 text-center">
                    <a href="{{ route('kasir.riwayat') }}" class="text-decoration-none transition-hover d-block">
                        <div class="rounded-3 d-flex align-items-center justify-content-center mx-auto mb-3 shadow-lg"
                            style="width: 160px; height: 160px;">
                            <img src="{{ asset('images/riwayat.png') }}" alt="Riwayat" class="img-fluid p-4">
                        </div>
                        <h4 class="fw-bold" style="color:#bf4646;">Riwayat</h4>
                        <p style="color: #a44040">Klik untuk melihat <br> Riwayat Transaksi</p>
                    </a>
                </div>

                <hr>
                <hr>

            </div>
        </div>
    </div>

    <style>
        .transition-hover {
            transition: 0.3s;
        }

        .transition-hover:hover {
            transform: scale(1.05);
        }
    </style>
</x-app-layout>
