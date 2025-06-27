<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;



use App\Models\Transaksi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    public function generatePDF()
    {
        $transaksis = Transaksi::with('details.product')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        $pdf = PDF::loadView('pdf.laporan-kasir', compact('transaksis'));
        return $pdf->stream('laporan-kasir.pdf');
    }
}

