<?php

namespace App\Filament\Widgets;

use App\Models\Transaksi;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;

class payment extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = '🧾 Metode Pembayaran';
    protected static ?int $sort = 5;

    protected function getData(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;
        $range = $this->filters['range'] ?? null;

        $query = Transaksi::where('payment_status', 'success');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);
        } elseif ($range) {
            $query = $this->applyRangeFilter($query, $range);
        }

        $paymentData = $query->selectRaw('payment_method, COUNT(*) as total')
            ->groupBy('payment_method')
            ->get();

        return [
            'labels' => $paymentData->pluck('payment_method')->toArray(),
            'datasets' => [
                [
                    'label' => 'Metode Pembayaran',
                    'data' => $paymentData->pluck('total')->toArray(),
                    'backgroundColor' => ['#f59e0b', '#10b981', '#3b82f6', '#ef4444'], // Warna untuk: cash, online, dsb
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
        return 'pie';
    }

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }
    protected function getHeight(): ?string
{
    return '100px'; // Adjust this value as needed, e.g., '250px', '40vh', etc.
}
}
