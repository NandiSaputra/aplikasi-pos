<?php

namespace App\Filament\Widgets;

use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;
        $range = $this->filters['range'] ?? null;

        $query = Transaksi::where('payment_status', 'success');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($range) {
            $query = $this->applyRangeFilter($query, $range);
        }

        $totalRevenue = $query->sum('total_price');
        $totalTransactions = $query->count();

        $itemQuery = TransaksiDetail::join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->where('transactions.payment_status', 'success');

        if ($startDate && $endDate) {
            $itemQuery->whereBetween('transactions.created_at', [$startDate, $endDate]);
        } elseif ($range) {
            $itemQuery = $this->applyRangeFilter($itemQuery, $range, 'transactions.created_at');
        }

        $totalItemsSold = $itemQuery->sum('transaction_details.quantity');

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

    protected function applyRangeFilter($query, string $range, string $column = 'created_at')
    {
        return match ($range) {
            'daily' => $query->whereDate($column, today()),
            'weekly' => $query->whereBetween($column, [now()->startOfWeek(), now()->endOfWeek()]),
            'monthly' => $query->whereMonth($column, now()->month)->whereYear($column, now()->year),
            'yearly' => $query->whereYear($column, now()->year),
            default => $query,
        };
    }

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }
}
