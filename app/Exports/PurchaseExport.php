<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PurchaseExport implements FromView
{
    public $purchases;
    public $totalSemua;
    public function __construct($purchases, $totalSemua)
    {
        $this->purchases = $purchases;
        $this->totalSemua = $totalSemua;
      
    }

    public function view(): View
    {
        return view('exports.purchase-excel', 
        ['purchases' => $this->purchases,
        'totalSemua' => $this->totalSemua
     
       ]);
    }
}
