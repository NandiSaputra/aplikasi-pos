<div class="p-6 bg-white rounded-xl shadow-md">

    <h2 class="text-2xl font-bold text-gray-800 mb-4">📊 Laporan Penjualan</h2>

    <div class="flex flex-wrap items-center gap-4 mb-6">
        <select wire:model="filter" class="px-4 py-2 border rounded-lg">
            <option value="hari">Hari Ini</option>
            <option value="minggu">Minggu Ini</option>
            <option value="bulan">Bulan Ini</option>
            <option value="tahun">Tahun Ini</option>
        </select>

        <button onclick="window.print()" class="bg-orange-500 text-white px-4 py-2 rounded">🖨️ Cetak</button>
        <a href="{{ route('laporan.export-excel') }}" class="bg-green-500 text-white px-4 py-2 rounded">📥 Export Excel</a>
    </div>

    <canvas id="penjualanChart" class="mb-6"></canvas>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const chart = new Chart(document.getElementById("penjualanChart"), {
                type: 'line',
                data: {
                    labels: @json(array_column($chartData, 'x')),
                    datasets: [{
                        label: 'Total Penjualan',
                        data: @json(array_column($chartData, 'y')),
                        borderColor: '#f97316',
                        fill: false,
                        tension: 0.3
                    }]
                },
            });
        });
    </script>

    <h3 class="text-xl font-semibold mt-8 mb-4">🔥 Top Produk Terlaris</h3>
    <ul class="list-disc ml-6 text-gray-700">
        @foreach($topProducts as $item)
            <li>{{ $item->product->name }} - {{ $item->total_qty }} Terjual</li>
        @endforeach
    </ul>

    <h3 class="text-xl font-semibold mt-8 mb-4">📄 Riwayat Transaksi</h3>
    <table class="min-w-full bg-white border">
        <thead class="bg-gray-100 text-gray-700 font-semibold text-sm">
            <tr>
                <th class="p-3 text-left">Invoice</th>
                <th class="p-3 text-left">Tanggal</th>
                <th class="p-3 text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $trx)
                <tr class="border-t">
                    <td class="p-3">{{ $trx->invoice_number }}</td>
                    <td class="p-3">{{ $trx->created_at->format('d M Y') }}</td>
                    <td class="p-3 text-right">Rp{{ number_format($trx->total_price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">{{ $transactions->links() }}</div>

</div>
