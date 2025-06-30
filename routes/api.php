<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MidtransWebhookController;

Route::post('/midtrans/webhook', [MidtransWebhookController::class, 'handle']);
Route::get('/midtrans/unfinish', function () {
 return redirect('/transaksi')->with('status', 'Pembayaran dibatalkan oleh pengguna.');
});
