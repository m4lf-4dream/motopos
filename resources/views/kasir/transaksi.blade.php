<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Point of Sale - Kasir') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card shadow-sm border-0 mb-3">
                            <div class="card-body">
                                <input type="text" id="search-product" class="form-control form-control-lg rounded-pill" placeholder="Cari sparepart atau merk...">
                            </div>
                        </div>

                        <div class="row" id="product-list">
                            @foreach($barangs as $barang)
                            <div class="col-md-4 mb-4 product-item" data-name="{{ strtolower($barang->nama_barang) }}">
                                <div class="card h-100 border-0 shadow-sm">
                                    <img src="{{ asset('storage/'.$barang->foto) }}" class="card-img-top" style="height: 150px; object-fit: cover;">
                                    <div class="card-body">
                                        <h6 class="fw-bold">{{ $barang->nama_barang }}</h6>
                                        <p class="text-primary fw-bold mb-1">Rp {{ number_format($barang->harga, 0, ',', '.') }}</p>
                                        <small class="text-muted d-block mb-3">Stok: {{ $barang->stok }}</small>
                                        <button class="btn btn-primary w-100 add-to-pos"
                                                data-id="{{ $barang->id }}"
                                                data-name="{{ $barang->nama_barang }}"
                                                data-price="{{ $barang->harga }}">
                                            <i class="bi bi-plus-lg"></i> Tambah
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card bg-primary text-white shadow-lg border-0 p-3" style="min-height: 80vh; border-radius: 20px;">
                            <h4 class="fw-bold mb-4"><i class="bi bi-cart3"></i> CHECKOUT</h4>

                            <div id="checkout-list" class="flex-grow-1 overflow-auto mb-3" style="max-height: 400px;">
                                </div>

                            <div class="border-top pt-3">
                                <div class="d-flex justify-content-between h5 mb-3">
                                    <span>TOTAL</span>
                                    <span class="fw-bold text-warning" id="total-price">Rp 0</span>
                                </div>

                                <div class="mb-3 text-dark">
                                    <label class="text-white mb-1 small">PEMBAYARAN (CASH)</label>
                                    <input type="number" id="input-pay" class="form-control form-control-lg" placeholder="Masukkan jumlah uang...">
                                </div>

                                <div class="mb-4">
                                    <label class="small mb-1">KEMBALIAN</label>
                                    <div class="h3 fw-bold text-white" id="text-change">Rp 0</div>
                                </div>

                                <button class="btn btn-warning btn-lg w-100 fw-bold py-3 text-primary" id="btn-confirm" disabled>
                                    KONFIRMASI PEMBAYARAN
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let cart = [];
        let totalPrice = 0;

        const searchInput = document.getElementById('search-product');
        const productItems = document.querySelectorAll('.product-item');

        searchInput.addEventListener('input', function() {
            const keyword = this.value.toLowerCase();
            productItems.forEach(item => {
                const productName = item.dataset.name;
                item.style.display = productName.includes(keyword) ? 'block' : 'none';
            });
        });

        document.addEventListener('click', function(e) {
            if (e.target && e.target.closest('.add-to-pos')) {
                const button = e.target.closest('.add-to-pos');
                const product = {
                    id: button.dataset.id,
                    name: button.dataset.name,
                    price: parseFloat(button.dataset.price),
                    quantity: 1
                };

                const existing = cart.find(item => item.id === product.id);
                if (existing) {
                    existing.quantity += 1;
                } else {
                    cart.push(product);
                }
                renderCart();
                calculateChange();
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
                    <div class="bg-white text-dark rounded p-2 mb-2 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold small text-truncate" style="max-width: 150px;">${item.name}</div>
                            <small class="text-muted">${item.quantity} x ${new Intl.NumberFormat('id-ID').format(item.price)}</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold small">Rp ${new Intl.NumberFormat('id-ID').format(subtotal)}</div>
                            <button class="btn btn-sm text-danger p-0 remove-item" data-index="${index}"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>
                `);
            });
            totalPriceText.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalPrice);
        }

        document.addEventListener('click', function(e) {
            if (e.target && e.target.closest('.remove-item')) {
                const index = e.target.closest('.remove-item').dataset.index;
                cart.splice(index, 1);
                renderCart();
                calculateChange();
            }
        });

        const inputPay = document.getElementById('input-pay');
        const textChange = document.getElementById('text-change');
        const btnConfirm = document.getElementById('btn-confirm');

        inputPay.addEventListener('input', calculateChange);

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
                textChange.className = 'h3 fw-bold text-danger';
                btnConfirm.disabled = true;
            }
        }

        btnConfirm.addEventListener('click', function() {
            if (!confirm('Konfirmasi pembayaran sekarang?')) return;

            fetch("{{ route('kasir.transaksi.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ items: JSON.stringify(cart) })
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    window.location.href = data.redirect;
                } else {
                    alert('Terjadi kesalahan: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal menghubungi server.');
            });
        });
    </script>
</x-app-layout>
