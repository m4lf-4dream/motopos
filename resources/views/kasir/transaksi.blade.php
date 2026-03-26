<x-app-layout>
    <style>
        :root {
            --primary-red: #bf4646;
            --cream-bg: #eddcc6;
        }
        body { background-color: var(--cream-bg) !important; }
        .bg-maroon { background-color: var(--primary-red) !important; }
        .text-maroon { color: var(--primary-red) !important; }
        .btn-maroon { background-color: var(--primary-red) !important; color: white !important; border: none; }
        .btn-maroon:hover { background-color: #a33b3b !important; }
        .card-custom { border-radius: 15px; border: none; }
        .checkout-container {
            background-color: var(--primary-red);
            color: white;
            min-height: 85vh;
            border-radius: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        /*scbar*/
        #list-antrean::-webkit-scrollbar { width: 5px; }
        #list-antrean::-webkit-scrollbar-thumb { background: #bf4646; border-radius: 10px; }
    </style>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Point of Sale - Kasir') }}
            </h2>
            <button id="btn-antrean" class="inline-flex items-center px-4 py-2 bg-white text-maroon border border-transparent rounded-md font-bold text-xs uppercase tracking-widest hover:bg-gray-100 transition duration-150">
                <i class="bi bi-people-fill mr-2"></i> Antrean Pesanan
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card card-custom shadow-sm mb-4">
                            <div class="card-body">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 rounded-start-pill">
                                        <i class="bi bi-search text-maroon"></i>
                                    </span>
                                    <input type="text" id="search-product" class="form-control form-control-lg border-start-0 rounded-end-pill" placeholder="Cari sparepart atau merk...">
                                </div>
                            </div>
                        </div>

                        <div class="row" id="product-list">
                            @foreach($barangs as $barang)
                            <div class="col-md-4 mb-4 product-item" data-name="{{ strtolower($barang->nama_barang) }}">
                                <div class="card h-100 card-custom shadow-sm overflow-hidden">
                                    <img src="{{ asset('storage/'.$barang->foto) }}" class="card-img-top" style="height: 160px; object-fit: cover;">
                                    <div class="card-body">
                                        <h6 class="fw-bold text-dark">{{ $barang->nama_barang }}</h6>
                                        <p class="text-maroon fw-bold mb-1">Rp {{ number_format($barang->harga, 0, ',', '.') }}</p>
                                        <small class="text-muted d-block mb-3">Stok: {{ $barang->stok }} pcs</small>
                                        <button class="btn btn-maroon w-100 add-to-pos shadow-sm"
                                                data-id="{{ $barang->id }}"
                                                data-name="{{ $barang->nama_barang }}"
                                                data-price="{{ $barang->harga }}">
                                            <i class="bi bi-plus-circle-fill me-1"></i> Tambah
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="checkout-container p-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="fw-bold m-0"><i class="bi bi-receipt-cutoff me-2"></i> CHECKOUT</h4>
                                <span class="badge bg-white text-maroon rounded-pill">Cashier Mode</span>
                            </div>

                            <div id="checkout-list" class="flex-grow-1 overflow-auto mb-4" style="max-height: 350px; scrollbar-width: thin;">
                            </div>

                            <div class="border-top border-white border-opacity-25 pt-4">
                                <div class="d-flex justify-content-between h5 mb-3">
                                    <span class="opacity-75">TOTAL</span>
                                    <span class="fw-bold text-warning" id="total-price" style="font-size: 1.5rem;">Rp 0</span>
                                </div>

                                <div class="mb-3">
                                    <label class="mb-1 small opacity-75">PEMBAYARAN (CASH)</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-white border-0 text-maroon fw-bold">Rp</span>
                                        <input type="number" id="input-pay" class="form-control border-0" placeholder="0">
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="small mb-1 opacity-75">KEMBALIAN</label>
                                    <div class="h3 fw-bold" id="text-change">Rp 0</div>
                                </div>

                                <button class="btn btn-light btn-lg w-100 fw-bold py-3 text-maroon shadow-lg" id="btn-confirm" disabled style="border-radius: 15px;">
                                    <i class="bi bi-check-all me-2"></i> SELESAIKAN TRANSAKSI
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAntrean" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md shadow-lg">
            <div class="modal-content" style="border-radius: 20px; border: none;">
                <div class="modal-header bg-maroon text-white" style="border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title fw-bold"><i class="bi bi-people-fill me-2"></i> Pesanan Masuk (Cash)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="list-antrean" style="background-color: #fcf8f3; max-height: 450px; overflow-y: auto;">
                </div>
            </div>
        </div>
    </div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let cart = [];
    let totalPrice = 0;
    let currentOrderId = null; // Tambahan: Melacak ID antrean yang dipilih

    const modalElement = document.getElementById('modalAntrean');
    const modalAntrean = new bootstrap.Modal(modalElement);

    // --- 1. SEARCH ---
    const searchInput = document.getElementById('search-product');
    const productItems = document.querySelectorAll('.product-item');

    if(searchInput) {
        searchInput.addEventListener('input', function() {
            const keyword = this.value.toLowerCase();
            productItems.forEach(item => {
                const productName = item.dataset.name;
                item.style.display = productName.includes(keyword) ? 'block' : 'none';
            });
        });
    }

    // --- 2. KERANJANG ---
    document.addEventListener('click', function(e) {
        const addBtn = e.target.closest('.add-to-pos');
        if (addBtn) {
            // Jika tambah barang manual, kita anggap bukan dari antrean
            if (cart.length === 0) currentOrderId = null;

            const product = {
                id: addBtn.dataset.id,
                name: addBtn.dataset.name,
                price: parseFloat(addBtn.dataset.price),
                quantity: 1
            };

            const existing = cart.find(item => item.id === product.id);
            if (existing) {
                existing.quantity += 1;
            } else {
                cart.push(product);
            }
            renderCart();
        }
    });

    function renderCart() {
        const checkoutList = document.getElementById('checkout-list');
        const totalPriceText = document.getElementById('total-price');
        checkoutList.innerHTML = '';
        totalPrice = 0;

        cart.forEach((item, index) => {
            const subtotal = item.price * item.quantity;
            totalPrice += subtotal;

            checkoutList.insertAdjacentHTML('beforeend', `
                <div class="bg-white text-dark rounded-3 p-3 mb-2 d-flex justify-content-between align-items-center shadow-sm">
                    <div style="flex: 1;">
                        <div class="fw-bold small text-truncate" style="max-width: 140px;">${item.name}</div>
                        <small class="text-muted">${item.quantity} x ${new Intl.NumberFormat('id-ID').format(item.price)}</small>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold small text-maroon">Rp ${new Intl.NumberFormat('id-ID').format(subtotal)}</div>
                        <button class="btn btn-sm text-danger p-0 remove-item" data-index="${index}"><i class="bi bi-x-circle-fill"></i></button>
                    </div>
                </div>
            `);
        });
        totalPriceText.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalPrice);
        calculateChange();
    }

    document.addEventListener('click', function(e) {
        const removeBtn = e.target.closest('.remove-item');
        if (removeBtn) {
            const index = removeBtn.dataset.index;
            cart.splice(index, 1);
            if (cart.length === 0) currentOrderId = null; // Reset ID jika keranjang kosong
            renderCart();
        }
    });

    // --- 3. PEMBAYARAN ---
    const inputPay = document.getElementById('input-pay');
    const textChange = document.getElementById('text-change');
    const btnConfirm = document.getElementById('btn-confirm');

    if(inputPay) {
        inputPay.addEventListener('input', calculateChange);
    }

    function calculateChange() {
        const payAmount = parseFloat(inputPay.value) || 0;
        const changeAmount = payAmount - totalPrice;

        if (totalPrice === 0) {
            textChange.innerText = 'Rp 0';
            textChange.className = 'h3 fw-bold text-white';
            btnConfirm.disabled = true;
        } else if (changeAmount >= 0) {
            textChange.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(changeAmount);
            textChange.className = 'h3 fw-bold text-white';
            btnConfirm.disabled = false;
        } else {
            textChange.innerText = 'Uang Kurang';
            textChange.className = 'h3 fw-bold text-warning';
            btnConfirm.disabled = true;
        }
    }

    // --- 4. ANTREAN ---
    const btnAntrean = document.getElementById('btn-antrean');
    if(btnAntrean) {
        btnAntrean.addEventListener('click', function() {
            fetch("{{ route('kasir.antrean') }}")
                .then(res => res.json())
                .then(data => {
                    const container = document.getElementById('list-antrean');
                    container.innerHTML = '';
                    if (Object.keys(data).length === 0) {
                        container.innerHTML = '<div class="text-center py-5"><p class="text-muted">Tidak ada pesanan masuk.</p></div>';
                    } else {
                        for (const orderId in data) {
                            let itemsHtml = '';
                            let totalOrder = 0;
                            data[orderId].forEach(item => {
                                totalOrder += parseFloat(item.total_harga);
                                itemsHtml += `<li>${item.barang.nama_barang} <span class="badge bg-secondary">${item.jumlah}x</span></li>`;
                            });

                            container.insertAdjacentHTML('beforeend', `
                                <div class="card mb-3 border-0 shadow-sm p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="fw-bold text-maroon m-0">${orderId}</h6>
                                        <span class="fw-bold text-dark">Rp ${new Intl.NumberFormat('id-ID').format(totalOrder)}</span>
                                    </div>
                                    <ul class="small text-muted ps-3 mb-3">${itemsHtml}</ul>
                                    <button class="btn btn-maroon btn-sm w-100 rounded-pill pilih-pesanan"
                                            data-orderid="${orderId}"
                                            data-items='${JSON.stringify(data[orderId])}'>
                                        Pindahkan ke Kasir
                                    </button>
                                </div>
                            `);
                        }
                    }
                    modalAntrean.show();
                })
                .catch(err => alert('Gagal mengambil data antrean.'));
        });
    }

    document.addEventListener('click', function(e) {
        const btnPilih = e.target.closest('.pilih-pesanan');
        if (btnPilih) {
            const items = JSON.parse(btnPilih.dataset.items);
            currentOrderId = btnPilih.dataset.orderid; // Simpan Order ID yang dipilih

            cart = [];
            items.forEach(item => {
                cart.push({
                    id: item.barang_id,
                    name: item.barang.nama_barang,
                    price: parseFloat(item.barang.harga),
                    quantity: parseInt(item.jumlah)
                });
            });
            renderCart();
            modalAntrean.hide();
        }
    });

    // --- 5. STORE (PROSES TRANSAKSI) ---
    if(btnConfirm) {
        btnConfirm.addEventListener('click', function() {
            if (!confirm('Selesaikan transaksi ini?')) return;

            fetch("{{ route('kasir.transaksi.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    items: JSON.stringify(cart),
                    bayar: inputPay.value,
                    order_id: currentOrderId // Kirim ID antrean ke backend untuk dihapus
                })
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    window.location.href = data.redirect;
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(err => alert('Terjadi kesalahan sistem.'));
        });
    }
});
</script>
</x-app-layout>
