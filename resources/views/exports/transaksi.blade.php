<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 13px;
        color: #333;
    }

    h2, h3 {
        color: #2f4f4f;
        margin-bottom: 5px;
        margin-top: 20px;
    }

    p {
        font-size: 12px;
        color: #555;
        margin-bottom: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }

    th, td {
        border: 1px solid #bbb;
        padding: 8px 10px;
    }

    thead {
        background-color: #2d6a4f;
        color: #fff;
        text-align: center;
    }

    tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    tbody td:nth-child(5),
    tbody td:nth-child(6),
    tbody td:nth-child(7),
    tfoot td,
    .summary-table td:last-child {
        text-align: right;
    }

    .summary-table td {
        border: 1px solid #bbb;
        padding: 8px 10px;
        background-color: #f1f5f9;
    }

    .summary-table tr:nth-child(even) {
        background-color: #e2e8f0;
    }

    .summary-table td:first-child {
        font-weight: bold;
        text-align: right;
    }
</style>

<h2>Laporan Penjualan</h2>
<p>Tanggal Laporan: {{ \Carbon\Carbon::now()->format('d F Y H:i') }}</p>

<h3>Detail Transaksi</h3>
<table>
    <thead>
        <tr>
            <th>Invoice</th>
            <th>Kasir</th>
            <th>Metode</th>
            <th>Status</th>
            <th>Diskon Kupon (Rp)</th>
            <th>Diskon Produk (Rp)</th>
            <th>Total (Netto) (Rp)</th>
            <th>Tanggal</th>
        </tr>
    </thead>
    <tbody>
        @forelse($transaksis as $trx)
            <tr>
                <td>{{ $trx->invoice_number }}</td>
                <td>{{ $trx->user->name ?? '-' }}</td>
                <td>{{ ucfirst($trx->payment_method) }}</td>
                <td>{{ ucfirst($trx->payment_status) }}</td>
                <td>Rp {{ number_format($trx->discount_amount, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($trx->product_discount_total, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($trx->total_price, 0, ',', '.') }}</td>
                <td>{{ \Carbon\Carbon::parse($trx->created_at)->format('d-m-Y H:i') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" style="text-align: center;">Tidak ada transaksi</td>
            </tr>
        @endforelse
    </tbody>
</table>

<h3>Ringkasan Penjualan</h3>
<table class="summary-table">
    <tbody>
        <tr>
            <td>Total Penjualan Kotor</td>
            <td>Rp {{ number_format($totalPenjualanKotor, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Total Diskon Kupon</td>
            <td>Rp {{ number_format($totalDiskonKupon, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Total Diskon Produk</td>
            <td>Rp {{ number_format($totalDiskonProduk, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td><strong>Total Penjualan Bersih</strong></td>
            <td><strong>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <td>Total Modal</td>
            <td>Rp {{ number_format($totalModal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td><strong>Laba Bersih</strong></td>
            <td><strong>Rp {{ number_format($labaBersih, 0, ',', '.') }}</strong></td>
        </tr>
    </tbody>
</table>
