<?php

namespace App\Filament\Widgets;

use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{ public ?string $filter = 'this_month';

    protected function getStats(): array
    {
        $query = Transaksi::where('payment_status', 'success');

        if ($this->filter === 'today') {
            $query->whereDate('created_at', today());
        } elseif ($this->filter === 'this_week') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($this->filter === 'this_month') {
            $query->whereMonth('created_at', now()->month);
        } elseif ($this->filter === 'this_year') {
            $query->whereYear('created_at', now()->year);
        }

        $totalRevenue = $query->sum('total_price');
        $totalTransactions = $query->count();

        $totalItemsSold = TransaksiDetail::join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->where('transactions.payment_status', 'success')
            ->when($this->filter === 'today', fn($q) => $q->whereDate('transactions.created_at', today()))
            ->when($this->filter === 'this_week', fn($q) => $q->whereBetween('transactions.created_at', [now()->startOfWeek(), now()->endOfWeek()]))
            ->when($this->filter === 'this_month', fn($q) => $q->whereMonth('transactions.created_at', now()->month))
            ->when($this->filter === 'this_year', fn($q) => $q->whereYear('transactions.created_at', now()->year))
            ->sum('transaction_details.quantity');

        return [
            Stat::make('Total Penjualan', 'Rp' . number_format($totalRevenue, 0, ',', '.'))
                ->description('Total nominal penjualan sukses')
                ->descriptionColor('success'),
            Stat::make('Transaksi Sukses', $totalTransactions)
                ->description('Jumlah transaksi sukses')
                ->descriptionColor('warning'),
            Stat::make('Produk Terjual', $totalItemsSold)
                ->description('Total item terjual')
                ->descriptionColor('info'),
        ];
    }

    public function getColumnSpan(): int | string | array
    {
        return 'full';
    }
}
