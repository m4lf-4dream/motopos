<x-app-layout>
    <style>
        /* Warna Dasar MOTOPART */
        :root {
            --maroon-moto: #BF4646;
            --cream-moto: #EDDCC6;
        }

        body {
            background-color: var(--cream-moto) !important;
        }

        /* Kustomisasi Scrollbar Maroon */
        .history-scroll-container {
            max-height: 550px;
            overflow-y: auto;
            padding-right: 10px;
        }

        .history-scroll-container::-webkit-scrollbar {
            width: 7px;
        }

        .history-scroll-container::-webkit-scrollbar-thumb {
            background: var(--maroon-moto);
            border-radius: 10px;
        }

        .history-scroll-container::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 10px;
        }

        /* Styling Kartu Pesanan */
        .order-card {
            border: none;
            border-radius: 15px;
            transition: transform 0.2s;
        }

        .badge-pending {
            background-color: #ffc107;
            color: #000;
        }

        .badge-success {
            background-color: #198754;
            color: #fff;
        }

        .text-maroon {
            color: var(--maroon-moto) !important;
        }
    </style>

    <div class="py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-maroon mb-0">Riwayat Pesanan</h2>
                    <p class="text-muted">Pantau status pesanan onderdil Anda di sini.</p>
                </div>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-dark fw-bold px-4" style="border-radius: 10px;">
                    <i class="bi bi-house-door-fill"></i> Home
                </a>
            </div>

            <div class="history-scroll-container">
                @forelse($orders as $order)
                    <div class="card order-card shadow-sm mb-3">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-2 border-end">
                                    <span class="text-muted small">ID Pesanan</span>
                                    <h5 class="fw-bold text-maroon mb-0">#{{ $order->id }}</h5>
                                    <span class="small text-muted">{{ $order->created_at->format('d M Y, H:i') }}</span>
                                </div>

                                <div class="col-md-5 ps-4">
                                    <span class="text-muted small">Daftar Barang</span>
                                    <ul class="list-unstyled mt-1 mb-0">
                                        @php
                                            // Mengasumsikan data barang disimpan dalam kolom 'items' dalam format JSON
                                            $items = json_decode($order->items);
                                        @endphp

                                        <div class="col-md-5 ps-4">
                                            <span class="text-muted small">Detail Barang</span>
                                            <ul class="list-unstyled mt-1 mb-0">
                                                <li class="d-flex justify-content-between border-bottom py-1">
                                                    <span>
                                                        <strong>{{ $order->barang->nama_barang ?? 'Produk Terhapus' }}</strong>
                                                        (x{{ $order->jumlah }})
                                                    </span>
                                                    <span class="text-muted small">
                                                        Rp {{ number_format($order->barang->harga ?? 0, 0, ',', '.') }}
                                                    </span>
                                                </li>
                                            </ul>
                                        </div>
                                    </ul>
                                </div>

                                <div class="col-md-3 text-center">
                                    <span class="text-muted small">Total Pembayaran</span>
                                    <h4 class="fw-bold mb-0">Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                                    </h4>
                                </div>

                                <div class="col-md-2 text-end">
                                    <span class="text-muted small d-block">Status</span>
                                    @if ($order->status == 'Pending')
                                        <span class="badge badge-pending px-3 py-2 rounded-pill shadow-sm">
                                            <i class="bi bi-clock-history"></i> Pending
                                        </span>
                                    @elseif($order->status == 'Selesai')
                                        <span class="badge badge-success px-3 py-2 rounded-pill shadow-sm">
                                            <i class="bi bi-check-circle-fill"></i> Selesai
                                        </span>
                                    @else
                                        <span
                                            class="badge bg-secondary px-3 py-2 rounded-pill">{{ $order->status }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 bg-white rounded-4 shadow-sm">
                        <i class="bi bi-bag-x text-muted display-1"></i>
                        <h4 class="mt-3 text-muted">Belum ada pesanan yang dibuat.</h4>
                        <a href="{{ route('dashboard') }}" class="btn btn-maroon mt-2 text-white"
                            style="background-color: var(--maroon-moto);">Mulai Belanja</a>
                    </div>
                @endforelse
            </div>

            <div class="mt-4 p-3 bg-white rounded-3 shadow-sm border-start border-4 border-danger">
                <small class="text-muted">
                    <i class="bi bi-info-circle-fill me-1"></i>
                    Jika status masih <strong>Pending</strong>, silakan tunjukkan <strong>ID Pesanan</strong> ke kasir
                    untuk melakukan pembayaran cash.
                </small>
            </div>
        </div>
    </div>
</x-app-layout>
