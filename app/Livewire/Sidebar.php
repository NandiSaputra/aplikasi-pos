<?php

namespace App\Livewire;

use Livewire\Component;

class Sidebar extends Component
{
    public $menu=[];

    public function mount(){
        
       $this->menu=[
        [
            'icon' => 'fa-solid fa-house',
            'label' => 'Dashboard',
            'route' => 'dashboard',
        ],
        [
            'icon' => 'fa-solid fa-clipboard',
            'label' => 'Transaksi',
            'route' => 'transaksi',
        ]];
   
    }
    public function render()
    {
        return view('livewire.sidebar');
    }
}
