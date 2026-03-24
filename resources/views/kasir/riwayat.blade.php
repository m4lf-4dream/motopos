<x-app-layout>
<x-slot name="header">
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Transaksi & Validasi Kasir') }}
        </h2>

        <div class="flex space-x-2">
            <a href="{{ route('kasir.transaksi') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Mulai Transaksi Baru
            </a>

            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Dashboard
            </a>
        </div>
    </div>
</x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse border border-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="border p-3 text-left">Order ID</th>
                                    <th class="border p-3 text-left">Barang</th>
                                    <th class="border p-3 text-left">Qty</th>
                                    <th class="border p-3 text-left">Total</th>
                                    <th class="border p-3 text-left">Metode</th>
                                    <th class="border p-3 text-left">Status</th>
                                    <th class="border p-3 text-left">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($semuaTransaksi as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="border p-3 text-sm font-mono">{{ $item->order_id }}</td>
                                    <td class="border p-3">
                                        <div class="font-bold">{{ $item->barang->nama_barang ?? 'Barang Terhapus' }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->barang->merk ?? '-' }}</div>
                                    </td>
                                    <td class="border p-3">{{ $item->jumlah }}</td>
                                    <td class="border p-3">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                                    <td class="border p-3">
                                        <span class="px-2 py-1 text-xs rounded {{ $item->metode_pembayaran == 'E-Money' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $item->metode_pembayaran }}
                                        </span>
                                    </td>
                                    <td class="border p-3">
                                        @if($item->status == 'Pending')
                                            <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">Pending</span>
                                        @elseif($item->status == 'Success')
                                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Success</span>
                                        @endif
                                    </td>
                                    <td class="border p-3">
                                        @if($item->metode_pembayaran == 'Cash' && $item->status == 'Pending')
                                            <form action="{{ route('kasir.konfirmasi', $item->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white text-xs py-1 px-3 rounded">
                                                    Konfirmasi
                                                </button>
                                            </form>
                                        @elseif($item->metode_pembayaran == 'E-Money' && $item->status == 'Pending')
                                            <span class="text-xs text-gray-400 italic">Wait Midtrans...</span>
                                        @else
                                            <span class="text-green-600 text-xs">✓ Selesai</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="border p-3 text-center text-gray-500">Belum ada transaksi.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
