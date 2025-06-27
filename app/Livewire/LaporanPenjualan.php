<?php
namespace App\Livewire;

use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class LaporanPenjualan extends Component
{
    use WithPagination;

    public $filter = 'bulan'; // hari, minggu, bulan, tahun
    public $startDate;
    public $endDate;

    public function mount()
    {
        $this->filter = 'bulan';
        $this->updateDateRange();
    }

    public function updatedFilter()
    {
        $this->updateDateRange();
    }

    public function updateDateRange()
    {
        $now = Carbon::now();

        switch ($this->filter) {
            case 'hari':
                $this->startDate = $now->copy()->startOfDay();
                $this->endDate = $now->copy()->endOfDay();
                break;
            case 'minggu':
                $this->startDate = $now->copy()->startOfWeek();
                $this->endDate = $now->copy()->endOfWeek();
                break;
            case 'bulan':
                $this->startDate = $now->copy()->startOfMonth();
                $this->endDate = $now->copy()->endOfMonth();
                break;
            case 'tahun':
                $this->startDate = $now->copy()->startOfYear();
                $this->endDate = $now->copy()->endOfYear();
                break;
        }
    }

    public function getChartData()
    {
        return Transaksi::where('user_id', Auth::id())
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('payment_status', 'success')
            ->selectRaw("DATE(created_at) as date, SUM(total_price) as total")
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn($item) => ['x' => $item->date, 'y' => $item->total])
            ->toArray();
    }

    public function render()
    {
        $transactions = Transaksi::where('user_id', Auth::id())
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->latest()
            ->paginate(10);

        $topProducts = TransaksiDetail::with('product')
            ->whereHas('transaksi', function ($q) {
                $q->where('user_id', Auth::id())
                  ->whereBetween('created_at', [$this->startDate, $this->endDate])
                  ->where('payment_status', 'success');
            })
            ->selectRaw('product_id, SUM(quantity) as total_qty')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        $chartData = $this->getChartData();

        return view('livewire.laporan-penjualan', compact('transactions', 'topProducts', 'chartData'));
    }
}
