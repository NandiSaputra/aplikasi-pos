<?php

namespace App\Filament\Widgets;

use App\Models\Transaksi;
use Filament\Widgets\ChartWidget;

class paymentMetode extends ChartWidget
{
    protected static ?string $heading = '💳 Metode Pembayaran';

    protected function getData(): array
    {
        $counts = Transaksi::where('payment_status', 'success')
            ->selectRaw('payment_method, COUNT(*) as total')
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method');

        return [
            'labels' => $counts->keys()->map(fn ($v) => ucfirst($v))->toArray(),
            'datasets' => [
                [
                    'label' => 'Pembayaran',
                    'data' => $counts->values()->toArray(),
                    'backgroundColor' => ['#10b981', '#f59e0b', '#ef4444'],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    public function getColumnSpan(): int | string | array
    {
        return [
            'md' => 2, // Tetap lebar
            'xl' => 1, // Donut chart tetap 1 kolom
        ];
    }
    
}
