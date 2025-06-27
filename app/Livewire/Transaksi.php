<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Transaksi extends Component
{
    public $search ='';
    public function render()
    {
        $transaksi = Transaksi::query()->where(
            'user_id', Auth::id()
        )->when($this->search, function ($query, $search) {
            $query->where('invoice_number', 'like', '%' . $search . '%');
        }) ->latest()
        ->get();
        return view('livewire.transaksi', compact($transaksi));
    }
}
