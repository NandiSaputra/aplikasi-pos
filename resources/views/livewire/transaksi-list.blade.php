<div class="p-6 bg-white rounded-xl shadow-md space-y-8">
    <h1 class="text-3xl font-extrabold text-gray-800">📒 Riwayat Transaksi</h1>

    <!-- Statistik Ringkas -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">
        <div class="bg-orange-50 border border-orange-200 rounded-xl p-4">
            <div class="text-xl font-bold text-orange-600">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</div>
            <div class="text-sm text-orange-800 font-semibold">Total Penjualan</div>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
            <div class="text-xl font-bold text-green-600">{{ $totalSuccess }}</div>
            <div class="text-sm text-green-800 font-semibold">Transaksi Sukses</div>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
            <div class="text-xl font-bold text-blue-600">{{ $totalProductsSold }}</div>
            <div class="text-sm text-blue-800 font-semibold">Produk Terjual</div>
        </div>
    </div>

    <!-- Filter -->
    <div class="grid md:grid-cols-5 gap-4">
        <!-- Cari Invoice -->
        <input type="text" wire:model.live="search"
            placeholder="🔍 Cari berdasarkan Invoice"
            class="px-4 py-2 border rounded-lg w-full focus:ring-orange-400">
    
        <!-- Status -->
        <select wire:model.live="status" class="px-4 py-2 border rounded-lg w-full">
            <option value="">📂 Semua Status</option>
            <option value="success">✅ Sukses</option>
            <option value="pending">⏳ Pending</option>
            <option value="failed">❌ Gagal</option>
            <option value="cancelled">🚫 Dibatalkan</option>
            <option value="expired">⌛ Kedaluwarsa</option>
        </select>
    
        <!-- Metode Pembayaran -->
        <select wire:model.live="metodePay" class="px-4 py-2 border rounded-lg w-full">
            <option value="">💳 Semua Metode</option>
            <option value="cash">💵 Tunai</option>
            <option value="online">🌐 Online</option>
        </select>
    
        <!-- Filter Tanggal Spesifik -->
        <input type="date" wire:model.live="dateFilter"
            placeholder="📅 Pilih tanggal spesifik"
            title="Memilih tanggal ini akan mengabaikan filter rentang waktu"
            class="px-4 py-2 border rounded-lg w-full">
    
        <!-- Rentang Waktu Otomatis -->
        <select wire:model.live="range" class="px-4 py-2 border rounded-lg w-full"
            title="Jika dipilih, filter tanggal spesifik akan diabaikan">
            <option value="">⏱ Tidak dipilih</option>
            <option value="daily">📆 Hari Ini</option>
            <option value="weekly">📅 Minggu Ini</option>
            <option value="monthly">🗓 Bulan Ini</option>
            <option value="yearly">📈 Tahun Ini</option>
        </select>
    </div>
    

    <!-- Tabel Transaksi -->
    <div class="overflow-x-auto border border-gray-200 rounded-lg">
        <table class="min-w-full divide-y divide-gray-200 text-sm text-gray-700">
            <thead class="bg-gray-50 text-left font-semibold text-gray-600">
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Invoice</th>
                    <th class="px-4 py-3">Total</th>
                    <th class="px-4 py-3">Metode</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($transaksis as $trx)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $loop->iteration + ($transaksis->firstItem() - 1) }}</td>
                        <td class="px-4 py-3 font-medium">{{ $trx->invoice_number }}</td>
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
                            <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold {{ $colorClass }}">
                                {{ ucfirst($trx->payment_status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $trx->created_at->format('d M Y, H:i') }}</td>
                        <td class="px-4 py-3">
                            <button wire:click="showDetail({{ $trx->id }})"
                                class="px-4 py-1.5 bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold rounded-lg shadow transition duration-150">
                                🔎 Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-gray-400 italic">Belum ada transaksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Grafik -->
 <!-- Grafik Penjualan -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
    <!-- Grafik Total Penjualan -->
    <div class="bg-white rounded-xl shadow-md border p-4 flex flex-col items-start">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            📈 Total Penjualan
        </h3>
        <div class="w-full">
            <canvas id="trxChart" class="w-full h-[200px] md:h-[250px]"></canvas>
        </div>
    </div>

    <!-- Grafik Produk Terlaris -->
    <div class="bg-white rounded-xl shadow-md border p-4 flex flex-col items-start">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            🔥 Produk Terlaris
        </h3>
        <div class="w-full">
            <canvas id="topProductsChart" class="w-full h-[200px] md:h-[250px]"></canvas>
        </div>
    </div>

    <!-- Grafik Metode Pembayaran -->
    <div class="bg-white rounded-xl shadow-md border p-4 flex flex-col items-start">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            💳 Metode Pembayaran
        </h3>
        <div class="w-full">
            <canvas id="paymentChart" class="w-full h-[200px] md:h-[250px]"></canvas>
        </div>
    </div>
</div>


    <!-- Pagination -->
    <div class="mt-6">
        {{ $transaksis->links() }}
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
                <div><span class="font-semibold block">Total</span>Rp{{ number_format($selectedTransaksi->total_price, 0, ',', '.') }}</div>
                <div><span class="font-semibold block">Metode Pembayaran</span>{{ ucfirst($selectedTransaksi->payment_method) }}</div>
                <div><span class="font-semibold block">Tanggal</span>{{ $selectedTransaksi->created_at->format('d M Y, H:i') }}</div>
                <div class="col-span-2 md:col-span-3">
                    <span class="font-semibold block mb-1">Status</span>
                    @php
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

            <div class="text-right mt-6">
                <button wire:click="closeModal" class="inline-block px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let trxChart, topProductsChart, paymentChart;

    function renderCharts(data) {
        trxChart?.destroy();
        topProductsChart?.destroy();
        paymentChart?.destroy();

        // Penjualan Chart
        trxChart = new Chart(document.getElementById('trxChart'), {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Total Penjualan Harian (Rp)',
                    data: data.values,
                    backgroundColor: 'rgba(255, 159, 64, 0.3)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 2,
                    pointRadius: 3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) => 'Rp' + value.toLocaleString()
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: (ctx) => 'Rp' + ctx.raw.toLocaleString()
                        }
                    }
                }
            }
        });

        // Produk Terlaris
        topProductsChart = new Chart(document.getElementById('topProductsChart'), {
            type: 'bar',
            data: {
                labels: data.topProducts.map(p => p.name),
                datasets: [{
                    label: 'Jumlah Terjual',
                    data: data.topProducts.map(p => p.qty),
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        precision: 0
                    }
                }
            }
        });

        // Metode Pembayaran
        paymentChart = new Chart(document.getElementById('paymentChart'), {
            type: 'pie',
            data: {
                labels: Object.keys(data.paymentMethods),
                datasets: [{
                    data: Object.values(data.paymentMethods),
                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#6366f1', '#8b5cf6'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { size: 12 }
                        }
                    }
                }
            }
        });
    }

    Livewire.on('updateCharts', data => renderCharts(data));

    document.addEventListener("DOMContentLoaded", () => {
        renderCharts({
            labels: @json($chartLabels),
            values: @json($chartValues),
            topProducts: @json($topProducts),
            paymentMethods: @json($paymentMethodCount),
        });
    });
</script>
@endpush

