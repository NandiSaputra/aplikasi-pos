<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
            font-size: 13px;
        }

        th, td {
            border: 1px solid #999;
            padding: 8px 10px;
            text-align: left;
        }

        thead th {
            background-color: #e0f2f1;
            font-weight: bold;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .right-align {
            text-align: right;
        }

        h2 {
            margin-bottom: 0;
        }

        p {
            margin-top: 4px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Laporan Pembelian</h2>
        <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d M Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Suplier</th>
                <th>Jumlah Produk</th>
                <th>Total Harga (Rp)</th>
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
                    <td>{{ $no++ }}</td>
                    <td>{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d-m-Y') }}</td>
                    <td>{{ $purchase->supplier->name ?? '-' }}</td>
                    <td class="right-align">{{ $jumlahProduk }}</td>
                    <td class="right-align">Rp {{ number_format($purchase->total_price, 0, ',', '.') }}</td>
                </tr>
            @endforeach

            {{-- Footer Total --}}
            <tr>
                <td colspan="3" class="right-align"><strong>Total Keseluruhan</strong></td>
                <td class="right-align"><strong>{{ $totalBarang }}</strong></td>
                <td class="right-align"><strong>Rp {{ number_format($totalSemua, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

</body>
</html>
