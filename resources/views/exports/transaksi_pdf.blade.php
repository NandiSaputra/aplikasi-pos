<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
        }

        h2, h3 {
            text-align: center;
            margin: 0;
            padding: 4px;
        }

        p {
            font-size: 11px;
            text-align: center;
            margin: 5px 0 15px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #999;
            padding: 6px;
            font-size: 11px;
        }

        thead {
            background-color: #eeeeee;
        }

        tbody tr:nth-child(even) {
            background-color: #f7f7f7;
        }

        .summary-table td {
            font-weight: bold;
            background-color: #f0f0f0;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }
    </style>
</head>
<body>

    <h2>Laporan Penjualan</h2>
    <p>Tanggal: {{ \Carbon\Carbon::now()->format('d F Y H:i') }}</p>

    <h3>Detail Transaksi</h3>
    <table>
        <thead>
            <tr>
                <th>Invoice</th>
                <th>Kasir</th>
                <th>Metode</th>
                <th>Status</th>
                <th class="right">Diskon Kupon</th>
                <th class="right">Diskon Produk</th>
                <th class="right">Total (Rp)</th>
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
                    <td class="right">Rp {{ number_format($trx->discount_amount, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($trx->product_discount_total, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</td>
                    <td>{{ \Carbon\Carbon::parse($trx->created_at)->format('d-m-Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="center">Tidak ada data transaksi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h3>Ringkasan Penjualan</h3>
    <table class="summary-table">
        <tbody>
            <tr>
                <td>Total Penjualan Kotor</td>
                <td class="right">Rp {{ number_format($totalPenjualanKotor, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Diskon Kupon</td>
                <td class="right">Rp {{ number_format($totalDiskonKupon, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Diskon Produk</td>
                <td class="right">Rp {{ number_format($totalDiskonProduk, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Penjualan Bersih</td>
                <td class="right">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Modal</td>
                <td class="right">Rp {{ number_format($totalModal, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Laba Bersih</td>
                <td class="right">Rp {{ number_format($labaBersih, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
