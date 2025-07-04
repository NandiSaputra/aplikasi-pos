<?php

namespace App\Filament\Widgets;

use App\Models\Purchase;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;
        $range = $this->filters['range'] ?? null;

        // Transaksi sukses
        $query = Transaksi::where('payment_status', 'success');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);
        } elseif ($range) {
            $query = $this->applyRangeFilter($query, $range);
        }

        $totalTransactions = $query->count();
        $totalRevenue = $query->sum('total_price');
     
        $totalKupon = $query->sum('discount_amount');
        $totalItemDiskon = $query->sum('product_discount_total');

        // Detail transaksi
        $itemQuery = TransaksiDetail::join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->where('transactions.payment_status', 'success');

        if ($startDate && $endDate) {
            $itemQuery->whereBetween('transactions.created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);
        } elseif ($range) {
            $itemQuery = $this->applyRangeFilter($itemQuery, $range, 'transactions.created_at');
        }
     
    
        $totalItemsSold = $itemQuery->sum('transaction_details.quantity');
    
        $totalgrossProfit = $itemQuery->selectRaw('
        SUM( 
            (COALESCE(transaction_details.price ) - COALESCE(transaction_details.buy_price, 0)) 
            * transaction_details.quantity
        ) as profit
    ')->value('profit');
    $totalProfit = $totalgrossProfit  - $totalKupon - $totalItemDiskon;

        // Pembelian dari suplier
        $purchaseQuery = Purchase::query();

        if ($startDate && $endDate) {
            $purchaseQuery->whereBetween('purchase_date', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);
        } elseif ($range) {
            $purchaseQuery = $this->applyRangeFilter($purchaseQuery, $range, 'purchase_date');
        }

        $totalPurchase = $purchaseQuery->sum('total_price');

        return [
            Stat::make('Total Laba', 'Rp' . number_format($totalProfit, 2, ',', '.'))
                ->description('Keuntungan bersih dari penjualan')
                ->descriptionColor('primary'),

            Stat::make('Total Penjualan', 'Rp' . number_format($totalRevenue, 2, ',', '.'))
                ->description('Total nominal penjualan sukses')
                ->descriptionColor('success'),

                Stat::make('Total Kupon', 'Rp' . number_format($totalKupon, 2, ',', '.'))
                ->description('Total nominal kupon di gunakan')
                ->descriptionColor('primary'),
                Stat::make('Total Diskon product', 'Rp' . number_format($totalItemDiskon, 2, ',', '.'))
                ->description('Total nominal Diskon di gunakan')
                ->descriptionColor('success'),

            Stat::make('Total Pembelian', 'Rp' . number_format($totalPurchase, 2, ',', '.'))
                ->description('Pembelian barang dari suplier')
                ->descriptionColor('danger'),

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
