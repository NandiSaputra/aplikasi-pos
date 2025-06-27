<div class="p-6 bg-white rounded-xl shadow-md">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Riwayat Transaksi</h1>

    <!-- Filter -->
    <div class="grid md:grid-cols-4 gap-4 mb-4">
        <input type="text" wire:model.live="search" placeholder="Cari Invoice..." class="px-4 py-2 border rounded-lg w-full">
    
        <select wire:model.live="status" class="px-4 py-2 border rounded-lg w-full">
            <option value="">Semua Status</option>
            <option value="success">Success</option>
            <option value="pending">Pending</option>
            <option value="failed">Failed</option>
            <option value="cancelled">Cancelled</option>
            <option value="expired">Expired</option>
        </select>
    
        <select wire:model.live="metodePay" class="px-4 py-2 border rounded-lg w-full">
            <option value="">Semua Metode</option>
            <option value="cash">Cash</option>
            <option value="online">Online</option>
        </select>
    
        <input type="date" wire:model.live="dateFilter" class="px-4 py-2 border rounded-lg w-full">
    </div>
    
    <!-- Tabel Transaksi -->
    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr class="text-left text-gray-600 text-sm font-semibold">
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Invoice</th>
                    <th class="px-4 py-3">Total</th>
                    <th class="px-4 py-3">Metode</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Tanggal</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100 text-sm text-gray-700">
                @forelse($transaksis as $index => $trx)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $loop->iteration + ($transaksis->firstItem() - 1) }}</td>
                        <td class="px-4 py-3">{{ $trx->invoice_number }}</td>
                        <td class="px-4 py-3">Rp{{ number_format($trx->total_price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 capitalize">{{ $trx->payment_method }}</td>
                        <td class="px-4 py-3">
                            @php
                                $statusColors = [
                                    'success' => 'bg-green-100 text-green-700',
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'failed' => 'bg-red-100 text-red-700',
                                    'cancelled' => 'bg-gray-100 text-gray-600',
                                    'expired' => 'bg-gray-100 text-gray-600',
                                ];
                                $colorClass = $statusColors[$trx->payment_status] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $colorClass }}">
                                @if($trx->payment_status === 'pending')
                                    <svg class="animate-spin w-3 h-3 mr-1 text-yellow-600" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                    </svg>
                                @endif
                                {{ ucfirst($trx->payment_status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $trx->created_at->format('d M Y, H:i') }}</td>
                        <td class="px-4 py-3">
                            <button 
                                wire:click="showDetail({{ $trx->id }})"
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-orange-500 text-white hover:bg-orange-200 hover:text-orange-800 text-xs font-semibold rounded-full transition duration-200"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Lihat Detail
                            </button>
                        </td>
                        
                        
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-400 italic">Belum ada transaksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
  <!-- Modal Detail Transaksi -->
@if($showModal && $selectedTransaksi)
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl mx-4 p-6 relative">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">
                Detail Transaksi - {{ $selectedTransaksi->invoice_number }}
            </h2>
            <button wire:click="closeModal" class="text-gray-500 hover:text-red-500 text-2xl leading-none">&times;</button>
        </div>

        <!-- Informasi Umum -->
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6 text-sm text-gray-700">
            <div>
                <span class="font-semibold block">Total</span>
                Rp{{ number_format($selectedTransaksi->total_price, 0, ',', '.') }}
            </div>
            <div>
                <span class="font-semibold block">Metode Pembayaran</span>
                {{ ucfirst($selectedTransaksi->payment_method) }}
            </div>
            <div>
                <span class="font-semibold block">Tanggal</span>
                {{ $selectedTransaksi->created_at->format('d M Y, H:i') }}
            </div>
            <div class="col-span-2 md:col-span-3">
                <span class="font-semibold block mb-1">Status</span>
                @php
                    $statusColors = [
                        'success' => 'bg-green-100 text-green-700',
                        'pending' => 'bg-orange-100 text-orange-700',
                        'failed' => 'bg-red-100 text-red-700',
                        'cancelled' => 'bg-gray-100 text-gray-600',
                        'expired' => 'bg-gray-100 text-gray-600',
                    ];
                    $colorClass = $statusColors[$selectedTransaksi->payment_status] ?? 'bg-gray-100 text-gray-700';
                @endphp
                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $colorClass }}">
                    {{ ucfirst($selectedTransaksi->payment_status) }}
                </span>
            </div>
        </div>

        <!-- Detail Produk -->
        <div class="overflow-x-auto border rounded-lg">
            <table class="min-w-full text-sm text-left text-gray-700">
                <thead class="bg-gray-50 text-gray-600 font-semibold">
                    <tr>
                        <th class="px-4 py-3">Produk</th>
                        <th class="px-4 py-3 text-center">Qty</th>
                        <th class="px-4 py-3 text-right">Harga</th>
                        <th class="px-4 py-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($selectedTransaksi->details as $detail)
                        <tr>
                            <td class="px-4 py-3">{{ $detail->product->name }}</td>
                            <td class="px-4 py-3 text-center">{{ $detail->quantity }}</td>
                            <td class="px-4 py-3 text-right">Rp{{ number_format($detail->price, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right">Rp{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Tombol Tutup -->
        <div class="text-right mt-6">
            <button wire:click="closeModal"
                class="inline-block px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg transition">
                Tutup
            </button>
        </div>
    </div>
</div>
@endif


    <!-- Pagination -->
    <div class="mt-6">
        {{ $transaksis->links() }}
    </div>
</div>
