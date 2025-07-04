<?php

namespace App\Filament\Widgets;

use App\Models\TransaksiDetail;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;

class bestSeller extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = '🔥 Produk Terlaris';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;
        $range = $this->filters['range'] ?? null;

        $query = TransaksiDetail::join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->where('transactions.payment_status', 'success');

        if ($startDate && $endDate) {
            $query->whereBetween('transactions.created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);
        } elseif ($range) {
            $query = $this->applyRangeFilter($query, $range, 'transactions.created_at');
        }

        $topProducts = $query
            ->selectRaw('products.name, SUM(transaction_details.quantity) as total')
            ->groupBy('products.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return [
            'labels' => $topProducts->pluck('name')->toArray(),
            'datasets' => [
                [
                    'label' => 'Jumlah Terjual',
                    'data' => $topProducts->pluck('total')->toArray(),
                    'backgroundColor' => '#f97316',
                ],
            ],
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

    protected function getType(): string
    {
        return 'bar';
    }
}
