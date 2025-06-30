<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MidtransRedirectController extends Controller
{
    public function unfinish(Request $request)
    {
        return redirect('/transaksi')->with('status', 'Pembayaran dibatalkan oleh pengguna.');
    }
}
