<x-app-layout>
    <script type="text/javascript"
    src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}">
</script>
    <style>
        /* SCROLLBAR KUSTOM UNTUK TABEL */
        .cart-scroll-container {
            max-height: 400px;
            overflow-y: auto;
            scrollbar-width: thin;
        }
        .cart-scroll-container::-webkit-scrollbar { width: 6px; }
        .cart-scroll-container::-webkit-scrollbar-thumb {
            background: #BF4646;
            border-radius: 10px;
        }
        .cart-scroll-container::-webkit-scrollbar-track { background: #f1f1f1; }
    </style>

    <div class="py-5" style="background-color: #EDDCC6; min-height: 100vh;">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold" style="color: #BF4646;"><i class="bi bi-cart4"></i> Keranjang Belanja</h2>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-dark fw-bold" style="border-radius: 10px;">
                    <i class="bi bi-arrow-left"></i> Kembali Belanja
                </a>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm rounded-4 p-3 mb-3">
                        <div class="cart-scroll-container">
                            <table class="table align-middle">
                                <thead class="sticky-top bg-white">
                                    <tr>
                                        <th>Produk</th>
                                        <th>Harga</th>
                                        <th width="80">Qty</th>
                                        <th>Subtotal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="cart-items-body">
                                    </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-white" style="background-color: #BF4646;">
                        <h4 class="fw-bold mb-4">Ringkasan</h4>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Total Harga:</span>
                            <span class="fw-bold fs-5" id="total-price-display">Rp 0</span>
                        </div>
                        <hr>

                        <div id="checkout-action-area">
                            <div id="checkout-action-area">
    <button id="btn-konfirmasi-cash" class="btn btn-light w-100 fw-bold py-3 shadow-sm mb-2" style="color: #BF4646; border-radius: 12px;">
        <i class="bi bi-cash-stack me-2"></i> KONFIRMASI PESANAN (CASH)
    </button>

    <button id="btn-konfirmasi-emoney" class="btn btn-dark w-100 fw-bold py-3 shadow-sm" style="border-radius: 12px; background-color: #1a1a1a;">
        <i class="bi bi-wallet2 me-2"></i> BAYAR DENGAN E-MONEY (QRIS/VA)
    </button>
</div>
                        </div>
                        <small class="d-block mt-3 text-center opacity-75">*Status pesanan akan menjadi 'Pending' sampai dibayar di Kasir.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>

        let cart = JSON.parse(localStorage.getItem('motopart_cart')) || [];

        document.getElementById('btn-konfirmasi-emoney').addEventListener('click', function() {
        if (cart.length === 0) return alert('Keranjang masih kosong!');

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menghubungkan...';

        fetch("{{ route('cart.checkout.midtrans') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                items: JSON.stringify(cart)
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                window.snap.pay(data.snap_token, {
                    onSuccess: function(result) {
                        alert("Pembayaran Berhasil!");
                        localStorage.removeItem('motopart_cart');
                        window.location.href = "{{ route('pembeli.pesanan') }}";
                    },
                    onPending: function(result) {
                        alert("Menunggu pembayaran Anda.");
                        localStorage.removeItem('motopart_cart');
                        window.location.href = "{{ route('pembeli.pesanan') }}";
                    },
                    onError: function(result) {
                        alert("Pembayaran gagal!");
                        btn.disabled = false;
                        btn.innerText = 'BAYAR DENGAN E-MONEY (QRIS/VA)';
                    },
                    onClose: function() {
                        alert('Anda menutup jendela pembayaran.');
                        btn.disabled = false;
                        btn.innerText = 'BAYAR DENGAN E-MONEY (QRIS/VA)';
                    }
                });
            } else {
                alert('Gagal: ' + data.message);
                btn.disabled = false;
                btn.innerText = 'BAYAR DENGAN E-MONEY (QRIS/VA)';
            }
        })
        .catch(err => {
            alert('Terjadi kesalahan koneksi.');
            btn.disabled = false;
            btn.innerText = 'BAYAR DENGAN E-MONEY (QRIS/VA)';
        });
    });

        function renderCart() {
            const body = document.getElementById('cart-items-body');
            const totalDisplay = document.getElementById('total-price-display');
            body.innerHTML = '';
            let total = 0;

            if (cart.length === 0) {
                body.innerHTML = '<tr><td colspan="5" class="text-center py-5 text-muted">Keranjang kosong.</td></tr>';
                totalDisplay.innerText = 'Rp 0';
                return;
            }

            cart.forEach((item, index) => {
                const subtotal = item.price * item.quantity;
                total += subtotal;
                body.insertAdjacentHTML('beforeend', `
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">${item.name}</div>
                        </td>
                        <td>Rp ${new Intl.NumberFormat('id-ID').format(item.price)}</td>
                        <td>
                            <input type="number" class="form-control form-control-sm" value="${item.quantity}"
                                   onchange="updateQty(${index}, this.value)" min="1" style="width: 60px;">
                        </td>
                        <td class="fw-bold text-maroon">Rp ${new Intl.NumberFormat('id-ID').format(subtotal)}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-danger border-0" onclick="removeItem(${index})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });

            totalDisplay.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
            localStorage.setItem('motopart_cart', JSON.stringify(cart));
        }

        function updateQty(index, val) {
            cart[index].quantity = parseInt(val);
            renderCart();
        }

        function removeItem(index) {
            if(confirm('Hapus barang ini?')) {
                cart.splice(index, 1);
                renderCart();
            }
        }

        // 2. Kirim Pesanan ke Kasir (Status Pending)
        document.getElementById('btn-konfirmasi-cash').addEventListener('click', function() {
            if (cart.length === 0) return alert('Keranjang masih kosong!');

            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Mengirim...';

            fetch("{{ route('cart.checkout.cash') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    items: JSON.stringify(cart),
                    payment: 'Cash'
                })
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    alert('Pesanan dikirim! Nomor Pesanan: ' + data.order_id);
                    localStorage.removeItem('motopart_cart');
                    window.location.href = "{{ route('pembeli.pesanan') }}";
                }
            })
            .catch(err => {
                alert('Terjadi kesalahan.');
                this.disabled = false;
                this.innerText = 'KONFIRMASI PESANAN (CASH)';
            });
        });

        document.addEventListener('DOMContentLoaded', renderCart);
    </script>
</x-app-layout>
