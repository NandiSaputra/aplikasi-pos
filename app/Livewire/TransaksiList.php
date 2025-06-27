<?php

namespace App\Livewire;

use App\Models\Transaksi;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class TransaksiList extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $dateFilter = '';
    public $metodePay = ''; // ✅ Tambahan
    public $selectedTransaksi = null;
    public $showModal = false;


    // Simpan filter di query string URL
    protected $queryString = ['search', 'status', 'dateFilter', 'metodePay'];

    public function updatingSearch()     { $this->resetPage(); }
    public function updatingStatus()     { $this->resetPage(); }
    public function updatingDateFilter() { $this->resetPage(); }
    public function updatingMetodePay()  { $this->resetPage(); } // ✅ Tambahan

    
    public function showDetail($id)
    {
        $this->selectedTransaksi = Transaksi::with('details.product')->find($id);
        $this->showModal = true;
    }
    
    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedTransaksi = null;
    }

    public function render()
    {
        $transaksis = Transaksi::query()
            ->where('user_id', Auth::id())
            ->when($this->search, fn($query) =>
                $query->where('invoice_number', 'like', '%' . $this->search . '%')
            )
            ->when($this->status, fn($query) =>
                $query->where('payment_status', $this->status)
            )
            ->when($this->metodePay, fn($query) =>
                $query->where('payment_method', $this->metodePay) // ✅ Filter by metode
            )
            ->when($this->dateFilter, fn($query) =>
                $query->whereDate('created_at', $this->dateFilter)
            )
            ->latest()
            ->paginate(10);

        return view('livewire.transaksi-list', compact('transaksis'));
    }
}
