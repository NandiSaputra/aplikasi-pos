<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\Products;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();

        // Validasi minimum field yang dibutuhkan
        if (!isset($payload['signature_key'], $payload['order_id'], $payload['status_code'], $payload['gross_amount'])) {
            Log::warning('Webhook Midtrans: Payload tidak lengkap.');
            return response()->json(['message' => 'Bad Request'], 400);
        }

        // Validasi signature Midtrans
        $serverKey = config('midtrans.serverKey');
        $expectedSignature = hash('sha512',
            $payload['order_id'] .
            $payload['status_code'] .
            $payload['gross_amount'] .
            $serverKey
        );

        if ($payload['signature_key'] !== $expectedSignature) {
            Log::warning('Webhook Midtrans: Signature tidak valid.');
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $orderId = $payload['order_id'];
        $transactionStatus = $payload['transaction_status'];

        $transaksi = Transaksi::where('invoice_number', $orderId)->first();

        if (!$transaksi) {
            Log::warning("Webhook Midtrans: Transaksi dengan invoice {$orderId} tidak ditemukan.");
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        // Hanya update jika belum sukses
        if ($transaksi->payment_status !== 'success') {
            switch ($transactionStatus) {
                case 'capture':
                case 'settlement':
                    $transaksi->update([
                        'payment_status' => 'success',
                    ]);

                    // Kurangi stok
                    foreach ($transaksi->details as $detail) {
                        $product = Products::find($detail->product_id);
                        if ($product) {
                            $product->decrement('stock', $detail->quantity);
                        }
                    }

                    Log::info("Transaksi #{$orderId} sukses. Stok dikurangi.");
                    break;

                case 'pending':
                    $transaksi->update(['payment_status' => 'pending']);
                    Log::info("Transaksi #{$orderId} pending.");
                    break;

                case 'deny':
                case 'cancel':
                case 'expire':
                    $transaksi->update(['payment_status' => 'failed']);
                    Log::info("Transaksi #{$orderId} gagal/expired/cancelled.");
                    break;
            }
        } else {
            Log::info("Transaksi #{$orderId} sudah success, tidak diubah.");
        }

        return response()->json(['message' => 'Webhook processed']);
    }
}
