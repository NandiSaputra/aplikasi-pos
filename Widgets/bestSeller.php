<?php

namespace App\Filament\Widgets;

use App\Models\TransaksiDetail;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class bestSeller extends ChartWidget
{
    protected static ?string $heading = '🔥 Produk Terlaris';

    protected function getData(): array
    {
        $topProducts = TransaksiDetail::join('products', 'transaction_details.product_id', '=', 'products.id')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->where('transactions.payment_status', 'success')
            ->select('products.name', DB::raw('SUM(transaction_details.quantity) as total'))
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
                    'backgroundColor' => '#3b82f6',
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public function getColumnSpan(): int | string | array
    {
        return [
            'md' => 2,
            'xl' => 1, // Sama, tampil di bawah grafik sebelumnya
        ];
    }
    
}
