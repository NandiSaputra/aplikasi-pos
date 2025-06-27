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

        // Signature validation
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

        // Cari transaksi berdasarkan invoice_number
        $transaksi = Transaksi::where('invoice_number', $orderId)->first();

        if (!$transaksi) {
            Log::warning('Transaksi tidak ditemukan untuk order_id: ' . $orderId);
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        // Proses status transaksi
        switch ($transactionStatus) {
            case 'capture':
            case 'settlement':
                if ($transaksi->payment_status !== 'success') {
                    $transaksi->update(['payment_status' => 'success']);

                    // Kurangi stok produk
                    foreach ($transaksi->detail as $detail) {
                        $product = Products::find($detail->product_id);
                        if ($product) {
                            $product->decrement('stock', $detail->quantity);
                        }
                    }

                    Log::info("Transaksi #{$orderId} berhasil & stok dikurangi.");
                }
                break;

            case 'pending':
                $transaksi->update(['payment_status' => 'pending']);
                Log::info("Transaksi #{$orderId} pending.");
                break;

            case 'deny':
            case 'expire':
            case 'cancel':
                $transaksi->update(['payment_status' => 'failed']);
                Log::info("Transaksi #{$orderId} gagal/expired/cancelled.");
                break;
        }

        return response()->json(['message' => 'Webhook processed']);
    }
}
