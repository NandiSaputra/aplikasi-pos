<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Transaksi;

class MidtransController extends Controller
{
    public function checkout(Request $request, Transaksi $order)
    {
        // Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.serverKey');
        Config::$isProduction = config('midtrans.isProduction');
        Config::$isSanitized = config('midtrans.isSanitized');
        Config::$is3ds = config('midtrans.is3ds');

        // Data transaksi ke Midtrans
        $params = [
            'transaction_details' => [
                'order_id' => $order->invoice_number,
                'gross_amount' => $order->total_price,
            ],
            'customer_details' => [
                'first_name' => 'Customer',
                'email' => 'customer@example.com',
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        return view('checkout', [
            'snapToken' => $snapToken,
            'order' => $order
        ]);
    }
}
