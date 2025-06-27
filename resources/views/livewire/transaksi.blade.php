<div class="p-6 bg-white rounded shadow">
    <h2 class="text-xl font-semibold mb-4">Daftar Transaksi Saya</h2>

    <input type="text" wire:model.live="search" placeholder="Cari Invoice..." class="mb-4 p-2 border rounded w-full">

    <table class="w-full table-auto border">
        <thead>
            <tr class="bg-gray-100 text-left">
                <th class="p-2">#</th>
                <th class="p-2">Invoice</th>
                <th class="p-2">Total</th>
                <th class="p-2">Pembayaran</th>
                <th class="p-2">Status</th>
                <th class="p-2">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksi as $index => $trx)
                <tr class="border-t">
                    <td class="p-2">{{ $index + 1 }}</td>
                    <td class="p-2">{{ $trx->invoice_number }}</td>
                    <td class="p-2">Rp{{ number_format($trx->total_price, 0, ',', '.') }}</td>
                    <td class="p-2 capitalize">{{ $trx->payment_method }}</td>
                    <td class="p-2 capitalize text-sm {{ $trx->payment_status === 'success' ? 'text-green-600' : 'text-yellow-500' }}">
                        {{ $trx->payment_status }}
                    </td>
                    <td class="p-2">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="p-4 text-center text-gray-500">Belum ada transaksi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

