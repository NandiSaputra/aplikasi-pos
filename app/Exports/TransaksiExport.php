<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TransaksiExport implements FromView
{
    public $transaksis;
    public $totalPendapatan;
    public $totalDiskonKupon;
    public $totalDiskonProduk;
    public $totalPenjualanKotor;
    public $totalModal;
    public $labaBersih;

    public function __construct($transaksis)
    {
        // 🔍 Filter hanya transaksi sukses
        $this->transaksis = $transaksis->where('payment_status', 'success');

        $this->totalDiskonKupon = $this->transaksis->sum(fn($trx) => floatval($trx->discount_amount));
        $this->totalDiskonProduk = $this->transaksis->sum(fn($trx) => floatval($trx->product_discount_total));

        // 💰 Penjualan kotor dari detail (harga × qty)
        $this->totalPenjualanKotor = $this->transaksis->sum(function ($trx) {
            return $trx->details->sum(function ($detail) {
                return floatval($detail->price) * intval($detail->quantity);
            });
        });

        // ✅ Penjualan bersih = kotor - diskon
        $this->totalPendapatan = $this->totalPenjualanKotor
                                    - $this->totalDiskonKupon
                                    - $this->totalDiskonProduk;

        // 🏭 Total modal
        $this->totalModal = $this->transaksis->sum(function ($trx) {
            return $trx->details->sum(function ($detail) {
                return floatval($detail->buy_price) * intval($detail->quantity);
            });
        });

        // 🔥 Laba bersih
        $this->labaBersih = $this->totalPendapatan - $this->totalModal;
    }

    public function view(): View
    {
        return view('exports.transaksi', [
            'transaksis' => $this->transaksis,
            'totalDiskonKupon' => $this->totalDiskonKupon,
            'totalDiskonProduk' => $this->totalDiskonProduk,
            'totalPenjualanKotor' => $this->totalPenjualanKotor,
            'totalPendapatan' => $this->totalPendapatan,
            'totalModal' => $this->totalModal,
            'labaBersih' => $this->labaBersih,
        ]);
    }
}
