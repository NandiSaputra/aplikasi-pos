<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pembelian</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
        }

        h2, p {
            text-align: center;
            margin: 2px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #999;
            padding: 6px;
        }

        thead th {
            background-color: #eaeaea;
            text-align: center;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        tfoot td {
            font-weight: bold;
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>

    <h2>Laporan Pembelian</h2>
    <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d-m-Y') }}</p>

    <table>
        <thead>
            <tr>
                <th class="center">No</th>
                <th class="center">Tanggal</th>
                <th>Suplier</th>
                <th class="right">Jumlah Barang</th>
                <th class="right">Total Harga (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                $totalBarang = 0;
            @endphp

            @foreach ($purchases as $purchase)
                @php
                    $jumlahProduk = $purchase->details->sum('quantity');
                    $totalBarang += $jumlahProduk;
                @endphp
                <tr>
                    <td class="center">{{ $no++ }}</td>
                    <td class="center">{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d-m-Y') }}</td>
                    <td>{{ $purchase->supplier->name ?? '-' }}</td>
                    <td class="right">{{ $jumlahProduk }}</td>
                    <td class="right">Rp {{ number_format($purchase->total_price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="right">Total Keseluruhan</td>
                <td class="right">{{ $totalBarang }}</td>
                <td class="right">Rp {{ number_format($totalSemua, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
