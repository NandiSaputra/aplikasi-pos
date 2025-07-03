<div class="p-4 sm:p-6 bg-gray-50 rounded-xl space-y-6">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">📒 Riwayat Transaksi</h1>

    <!-- Statistik -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">
        <div class="bg-white rounded-xl p-4 shadow">
            <div class="text-lg sm:text-xl font-bold text-gray-800">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</div>
            <div class="text-sm text-gray-500">Total Penjualan</div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow">
            <div class="text-lg sm:text-xl font-bold text-green-600">{{ $totalSuccess }}</div>
            <div class="text-sm text-gray-500">Transaksi Sukses</div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow">
            <div class="text-lg sm:text-xl font-bold text-blue-600">{{ $totalProductsSold }}</div>
            <div class="text-sm text-gray-500">Produk Terjual</div>
        </div>
    </div>

    <!-- Filter -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
        <input type="text" wire:model.live="search" placeholder="🔍 Cari Invoice" class="px-3 py-2 border rounded-xl text-sm w-full">
        <select wire:model.live="status" class="px-3 py-2 border rounded-xl text-sm w-full">
            <option value="">📂 Semua Status</option>
            <option value="success">✅ Sukses</option>
            <option value="pending">⏳ Pending</option>
            <option value="failed">❌ Gagal</option>
            <option value="cancelled">🚫 Dibatalkan</option>
            <option value="expired">⌛ Kedaluwarsa</option>
        </select>
        <select wire:model.live="metodePay" class="px-3 py-2 border rounded-xl text-sm w-full">
            <option value="">💳 Semua Metode</option>
            <option value="cash">💵 Tunai</option>
            <option value="online">🌐 Online</option>
        </select>
        <input type="date" wire:model.live="dateFilter" class="px-3 py-2 border rounded-xl text-sm w-full">
        <select wire:model.live="range" class="px-3 py-2 border rounded-xl text-sm w-full">
            <option value="">⏱ Tidak dipilih</option>
            <option value="daily">📆 Hari Ini</option>
            <option value="weekly">📅 Minggu Ini</option>
            <option value="monthly">🗓 Bulan Ini</option>
            <option value="yearly">📈 Tahun Ini</option>
        </select>
    </div>

    <!-- Grafik -->
    <div class="bg-white rounded-xl shadow p-4 h-64">
        <h2 class="text-sm font-semibold mb-2">📈 Total Penjualan</h2>
        <div class="relative h-full">
            <canvas id="trxChart" class="absolute inset-0 w-full h-full mb-5"></canvas>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-4 h-64">
        <h2 class="text-sm font-semibold mb-2">🔥 Produk Terlaris</h2>
        <div class="relative h-full">
            <canvas id="topProductsChart" class="absolute inset-0 w-full h-full mb-5"></canvas>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-4 h-64">
        <h2 class="text-sm font-semibold mb-2">📦 Semua Produk Terjual</h2>
        <div class="relative h-full">
            <canvas id="allProductsChart" class="absolute inset-0 w-full h-full mb-5"></canvas>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-4 h-64">
        <h2 class="text-sm font-semibold mb-2">💳 Metode Pembayaran</h2>
        <div class="relative h-full">
            <canvas id="paymentChart" class="absolute inset-0 w-full h-full mb-5"></canvas>
        </div>
    </div>

    <!-- Tabel Transaksi -->
   <!-- Tabel Transaksi -->
<div class="bg-white rounded-xl shadow p-4 overflow-x-auto">
    <h2 class="text-sm font-semibold mb-4">📋 Daftar Transaksi</h2>
    <table class="w-full text-sm text-left border-t border-gray-200">
        <thead>
            <tr class="text-gray-600 bg-gray-100">
                <th class="px-4 py-3">#</th>
                <th class="px-4 py-3">Invoice</th>
                <th class="px-4 py-3">Tanggal</th>
                <th class="px-4 py-3">Total</th>
                <th class="px-4 py-3">Pembayaran</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transaksis as $index => $trx)
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-4 py-2">{{ $transaksis->firstItem() + $index }}</td>
                    <td class="px-4 py-2 font-medium text-gray-800">{{ $trx->invoice_number }}</td>
                    <td class="px-4 py-2">{{ \Carbon\Carbon::parse($trx->created_at)->translatedFormat('d M Y H:i') }}</td>
                    <td class="px-4 py-2 text-orange-600 font-semibold">
                        Rp{{ number_format($trx->total_price, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-2">
                        @if ($trx->payment_method === 'cash')
                            💵 Tunai
                        @else
                            🌐 Online
                        @endif
                    </td>
                    <td class="px-4 py-2">
                        @php
                            $statusColor = [
                                'success' => 'text-green-600',
                                'pending' => 'text-yellow-600',
                                'failed' => 'text-red-600',
                                'cancelled' => 'text-gray-500',
                                'expired' => 'text-gray-500',
                            ][$trx->payment_status] ?? 'text-gray-700';
                        @endphp
                        <span class="font-semibold {{ $statusColor }}">
                            {{ ucfirst($trx->payment_status) }}
                        </span>
                    </td>
                    <td class="px-4 py-2 text-right">
                        <button wire:click="showDetail({{ $trx->id }})"
                            class="text-blue-600 hover:underline text-sm font-medium">
                            Detail
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                        Tidak ada transaksi ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $transaksis->links() }}
    </div>
</div>
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let trxChart, topProductsChart, paymentChart, allProductsChart;

        function renderCharts(data) {
            trxChart?.destroy();
            topProductsChart?.destroy();
            paymentChart?.destroy();
            allProductsChart?.destroy();

            trxChart = new Chart(document.getElementById('trxChart'), {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Penjualan Harian',
                        data: data.values,
                        borderColor: '#f97316',
                        backgroundColor: 'rgba(251, 146, 60, 0.2)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { mode: 'index', intersect: false }
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });

            topProductsChart = new Chart(document.getElementById('topProductsChart'), {
                type: 'bar',
                data: {
                    labels: data.topProducts.map(p => p.name),
                    datasets: [{
                        label: 'Jumlah Terjual',
                        data: data.topProducts.map(p => p.qty),
                        backgroundColor: '#60a5fa',
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });

            allProductsChart = new Chart(document.getElementById('allProductsChart'), {
                type: 'bar',
                data: {
                    labels: data.allProducts.map(p => p.name),
                    datasets: [{
                        label: 'Jumlah Terjual',
                        data: data.allProducts.map(p => p.qty),
                        backgroundColor: '#f97316',
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });

            paymentChart = new Chart(document.getElementById('paymentChart'), {
                type: 'pie',
                data: {
                    labels: Object.keys(data.paymentMethods),
                    datasets: [{
                        data: Object.values(data.paymentMethods),
                        backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#6366f1']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { usePointStyle: true }
                        }
                    }
                }
            });
        }

        Livewire.on('updateCharts', renderCharts);

        document.addEventListener("DOMContentLoaded", () => {
            renderCharts({
                labels: @json($chartLabels ?? []),
                values: @json($chartValues ?? []),
                topProducts: @json($topProducts ?? []),
                allProducts: @json($allProducts ?? []),
                paymentMethods: @json($paymentMethodCount ?? [])
            });
        });
    </script>
    @endpush
</div>
