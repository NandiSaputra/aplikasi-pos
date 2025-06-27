<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;

class PaymentController extends Controller
{
    public function pending(Request $request)
    {
        $invoice = $request->query('invoice');
        $transaksi = Transaksi::where('invoice_number', $invoice)->firstOrFail();

        // Midtrans config
        MidtransConfig::$serverKey = config('midtrans.serverKey');
        MidtransConfig::$isProduction = false;
        MidtransConfig::$isSanitized = true;
        MidtransConfig::$is3ds = true;

        // Generate Snap Token
        $params = [
            'transaction_details' => [
                'order_id' => $transaksi->invoice_number,
                'gross_amount' => $transaksi->total_price,
            ],
            'customer_details' => [
                'first_name' => $transaksi->user->name ?? 'Guest',
                'email' => $transaksi->user->email ?? 'guest@example.com',
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        return view('payment.pending', [
            'invoice' => $invoice,
            'snapToken' => $snapToken,
        ]);
    }

    public function cancel(Request $request)
    {
        $invoice = $request->input('invoice');
        $transaksi = Transaksi::where('invoice_number', $invoice)->firstOrFail();

        $transaksi->update([
            'payment_status' => 'cancelled',
        ]);

        return redirect('/dashboard')->with('status', 'Pembayaran dibatalkan.');
    }
}
