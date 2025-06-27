<?php

namespace App\Livewire;

use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class TransaksiList extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $dateFilter = ''; // format: Y-m-d (bisa dikembangkan jadi range)
    public $metodePay = '';
    public $range = 'daily';
    public $selectedTransaksi = null;
    public $showModal = false;

    protected $queryString = ['search', 'status', 'dateFilter', 'metodePay', 'range'];

    public function updatingSearch()     { $this->resetPage(); }
    public function updatingStatus()     { $this->resetPage(); }
    public function updatingDateFilter() { $this->resetPage(); }
    public function updatingMetodePay()  { $this->resetPage(); }
    public function updatingRange()      { $this->resetPage(); }

    // Reset otomatis jika salah satu filter dipilih
    public function updatedDateFilter($value)
    {
        if (!empty($value)) {
            $this->range = null; // kosongkan range jika dateFilter aktif
        }
    }

    public function updatedRange($value)
    {
        if (!empty($value)) {
            $this->dateFilter = null; // kosongkan dateFilter jika range aktif
        }
    }

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

    public function getDateRange()
    {
        if (!$this->range) return [null, null];

        $today = Carbon::today();

        return match ($this->range) {
            'daily'   => [$today->copy()->startOfDay(), $today->copy()->endOfDay()],
            'weekly'  => [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()],
            'monthly' => [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()],
            'yearly'  => [$today->copy()->startOfYear(), $today->copy()->endOfYear()],
            default   => [null, null],
        };
    }

    public function render()
    {
        [$startDate, $endDate] = $this->getDateRange();

        // Transaksi utama
        $transaksis = Transaksi::query()
            ->where('user_id', Auth::id())
            ->when($this->search, fn($q) =>
                $q->where('invoice_number', 'like', '%' . $this->search . '%')
            )
            ->when($this->status, fn($q) =>
                $q->where('payment_status', $this->status)
            )
            ->when($this->metodePay, fn($q) =>
                $q->where('payment_method', $this->metodePay)
            )
            ->when($this->dateFilter, fn($q) =>
                $q->whereDate('created_at', $this->dateFilter)
            )
            ->when($startDate && $endDate, fn($q) =>
                $q->whereBetween('created_at', [$startDate, $endDate])
            )
            ->latest()
            ->paginate(10);

        // Statistik dasar
        $baseStats = Transaksi::where('user_id', Auth::id())
            ->where('payment_status', 'success')
            ->when($this->dateFilter, fn($q) =>
                $q->whereDate('created_at', $this->dateFilter)
            )
            ->when($startDate && $endDate, fn($q) =>
                $q->whereBetween('created_at', [$startDate, $endDate])
            );

        $totalRevenue = $baseStats->sum('total_price');
        $totalSuccess = $baseStats->count();

        // Jumlah produk terjual
        $totalProductsSold = TransaksiDetail::whereHas('transaksi', function ($q) use ($startDate, $endDate) {
            $q->where('user_id', Auth::id())
              ->where('payment_status', 'success');

            if ($this->dateFilter) {
                $q->whereDate('created_at', $this->dateFilter);
            } elseif ($startDate && $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }
        })->sum('quantity');

        // Data grafik penjualan
        $chartData = Transaksi::selectRaw('DATE(created_at) as date, SUM(total_price) as total')
            ->where('user_id', Auth::id())
            ->where('payment_status', 'success')
            ->when($this->dateFilter, fn($q) =>
                $q->whereDate('created_at', $this->dateFilter)
            )
            ->when($startDate && $endDate, fn($q) =>
                $q->whereBetween('created_at', [$startDate, $endDate])
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $chartLabels = $chartData->pluck('date')->map(fn($d) =>
            Carbon::parse($d)->translatedFormat('d M')
        )->toArray();

        $chartValues = $chartData->pluck('total')->map(fn($v) =>
            round($v, 2)
        )->toArray();

        // Produk terlaris
        $topProducts = TransaksiDetail::select('product_id', DB::raw('SUM(quantity) as qty'))
            ->whereHas('transaksi', function ($q) use ($startDate, $endDate) {
                $q->where('user_id', Auth::id())
                  ->where('payment_status', 'success');

                if ($this->dateFilter) {
                    $q->whereDate('created_at', $this->dateFilter);
                } elseif ($startDate && $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate]);
                }
            })
            ->with('product:id,name')
            ->groupBy('product_id')
            ->orderByDesc('qty')
            ->limit(5)
            ->get()
            ->map(fn($item) => (object)[
                'name' => $item->product->name,
                'qty' => (int)$item->qty,
            ]);

        // Metode pembayaran chart
        $paymentMethodCount = Transaksi::where('user_id', Auth::id())
            ->where('payment_status', 'success')
            ->when($this->dateFilter, fn($q) =>
                $q->whereDate('created_at', $this->dateFilter)
            )
            ->when($startDate && $endDate, fn($q) =>
                $q->whereBetween('created_at', [$startDate, $endDate])
            )
            ->select('payment_method', DB::raw('COUNT(*) as total'))
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method')
            ->toArray();

        // Kirim data chart ke JS
        $this->dispatch('updateCharts', [
            'labels' => $chartLabels,
            'values' => $chartValues,
            'topProducts' => $topProducts,
            'paymentMethods' => $paymentMethodCount,
        ]);

        return view('livewire.transaksi-list', [
            'transaksis' => $transaksis,
            'totalRevenue' => $totalRevenue,
            'totalSuccess' => $totalSuccess,
            'totalProductsSold' => $totalProductsSold,
            'chartLabels' => $chartLabels,
            'chartValues' => $chartValues,
            'topProducts' => $topProducts,
            'paymentMethodCount' => $paymentMethodCount,
        ]);
    }
}
