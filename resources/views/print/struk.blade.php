<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Struk Pembelian</title>
  
  <style>
        body {
            font-family: monospace;
            font-size: 12px;
            max-width: 250px;
            margin: auto;
        }
        h2, p, table {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            padding: 2px 0;
        }
        .total {
            font-weight: bold;
            border-top: 1px dashed black;
            margin-top: 5px;
        }
    </style>
     
</head>
<body>
    <h2>STRUK PEMBELIAN</h2>
    <p>{{ $transaksi->invoice_number }}</p>
    <p>{{ $transaksi->created_at->format('d-m-Y') }}</p>

    <hr>

    <table>
        @php
            $totalSebelumDiskon = 0;
        @endphp

        @foreach ($transaksi->details as $item)
            @php
                $hargaAsli = $item->price;
                $subtotal = $item->subtotal;
                $totalSebelumDiskon += $hargaAsli * $item->quantity;
            @endphp
            <tr>
                <td colspan="2">{{ $item->product->name }}</td>
            </tr>
            <tr>
                <td>{{ $item->quantity }} x Rp{{ number_format($hargaAsli, 0, ',', '.') }}</td>
                <td style="text-align: right;">Rp{{ number_format($subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </table>

    <hr>
    <table>
        <tr>
            <td>Subtotal</td>
            <td style="text-align: right;">Rp{{ number_format($totalSebelumDiskon, 0, ',', '.') }}</td>
        </tr>
        @if ($transaksi->product_discount_total > 0)
        <tr>
            <td>Diskon Produk</td>
            <td style="text-align: right;">-Rp{{ number_format($transaksi->product_discount_total, 0, ',', '.') }}</td>
        </tr>
        @endif
        @if ($transaksi->discount_amount > 0)
        <tr>
            <td>Diskon Kupon</td>
            <td style="text-align: right;">-Rp{{ number_format($transaksi->discount_amount, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr class="total">
            <td>Total</td>
            <td style="text-align: right;">Rp{{ number_format($transaksi->total_price, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Bayar</td>
            <td style="text-align: right;">Rp{{ number_format($transaksi->paid_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Kembalian</td>
            <td style="text-align: right;">Rp{{ number_format($transaksi->change_amount, 0, ',', '.') }}</td>
        </tr>
    </table>

    <hr>
    <p>Terima kasih telah berbelanja!</p>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
