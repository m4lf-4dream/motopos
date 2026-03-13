<x-app-layout>
    <div class="container-fluid py-5" style="background-color: #EDDCC6; min-height: 100vh;">
        <div class="container">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold" style="color: #BF4646;">Manajemen Stok Barang</h2>
                <a href="{{ route('dashboard') }}" class="btn text-white px-4 shadow-sm"
                    style="background-color: #BF4646; border-radius: 10px;">
                    Kembali ke Dashboard
                </a>
            </div>

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 p-3">
                        <div class="card-body">
                            <h5 class="fw-bold mb-4" style="color: #BF4646;">Tambah Barang Baru</h5>

                            <form action="{{ route('kasir.barang.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-secondary">Kode Barang</label>
                                    <input type="text" name="kode_barang" class="form-control border-0 bg-light p-2"
                                        placeholder="Contoh: VAR01" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-secondary">Nama Barang</label>
                                    <input type="text" name="nama_barang" class="form-control border-0 bg-light p-2"
                                        placeholder="Nama Sparepart" required>
                                </div>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label class="form-label fw-semibold text-secondary">Merk</label>
                                        <input type="text" name="merk" class="form-control border-0 bg-light p-2"
                                            placeholder="Merk" required>
                                    </div>
                                    <div class="col">
                                        <label class="form-label fw-semibold text-secondary">Warna</label>
                                        <input type="text" name="warna" class="form-control border-0 bg-light p-2"
                                            placeholder="Warna" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label class="form-label fw-semibold text-secondary">Harga</label>
                                        <input type="number" name="harga" class="form-control border-0 bg-light p-2"
                                            placeholder="Rp" required>
                                    </div>
                                    <div class="col">
                                        <label class="form-label fw-semibold text-secondary">Stok</label>
                                        <input type="number" name="stok" class="form-control border-0 bg-light p-2"
                                            placeholder="0" required>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold text-secondary">Foto Barang</label>
                                    <input type="file" name="foto" class="form-control border-0 bg-light p-2"
                                        required>
                                </div>
                                <button type="submit" class="btn w-100 text-white fw-bold py-2 shadow"
                                    style="background-color: #BF4646; border-radius: 12px;">
                                    Simpan Barang
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead style="background-color: #BF4646;">
                                    <tr>
                                        <th class="text-white px-4 py-3 border-0">Foto</th>
                                        <th class="text-white py-3 border-0">Info Barang</th>
                                        <th class="text-white py-3 border-0 text-center">Stok</th>
                                        <th class="text-white py-3 border-0">Harga</th>
                                        <th class="text-white px-4 py-3 border-0 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($barangs as $item)
                                        <tr>
                                            <td class="px-4 py-3">
                                                <img src="{{ asset('storage/barangs/' . $item->foto) }}" alt="Foto"
                                                    class="rounded-3 shadow-sm"
                                                    style="width: 60px; height: 60px; object-fit: cover;">
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $item->nama_barang }}</div>
                                                <small class="text-muted">{{ $item->kode_barang }} | {{ $item->merk }}
                                                    ({{ $item->warna }})</small>
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="badge rounded-pill bg-light text-dark border px-3">{{ $item->stok }}</span>
                                            </td>
                                            <td class="fw-bold text-dark">
                                                Rp{{ number_format($item->harga, 0, ',', '.') }}</td>
                                            <td class="text-center px-4">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <button type="button" class="btn btn-sm btn-warning text-white"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editModal{{ $item->id }}">
                                                        Edit
                                                    </button>

                                                    <form action="{{ route('kasir.barang.destroy', $item->id) }}"
                                                        method="POST" onsubmit="return confirm('Hapus barang ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-sm btn-danger">Hapus</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content border-0" style="border-radius: 20px;">
                                                    <div class="modal-header text-white border-0"
                                                        style="background-color: #BF4646; border-radius: 20px 20px 0 0;">
                                                        <h5 class="modal-title fw-bold">Edit Barang:
                                                            {{ $item->nama_barang }}</h5>
                                                        <button type="button" class="btn-close btn-close-white"
                                                            data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('kasir.barang.update', $item->id) }}"
                                                        method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body" style="background-color: #EDDCC6;">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Nama Barang</label>
                                                                <input type="text" name="nama_barang"
                                                                    class="form-control"
                                                                    value="{{ $item->nama_barang }}" required>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="form-label fw-bold">Harga</label>
                                                                    <input type="number" name="harga"
                                                                        class="form-control"
                                                                        value="{{ $item->harga }}" required>
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="form-label fw-bold">Stok</label>
                                                                    <input type="number" name="stok"
                                                                        class="form-control"
                                                                        value="{{ $item->stok }}" required>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Ganti Foto
                                                                    (Opsional)</label>
                                                                <input type="file" name="foto"
                                                                    class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0"
                                                            style="background-color: #EDDCC6; border-radius: 0 0 20px 20px;">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn text-white px-4"
                                                                style="background-color: #BF4646;">Simpan
                                                                Perubahan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-secondary italic">Belum
                                                ada data barang.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
