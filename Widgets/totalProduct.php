<?php

namespace App\Filament\Widgets;

use App\Models\TransaksiDetail;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class totalProduct extends ChartWidget
{
    protected static ?string $heading = '📦 Semua Produk Terjual';

    protected function getData(): array
    {
        $products = TransaksiDetail::join('products', 'transaction_details.product_id', '=', 'products.id')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->where('transactions.payment_status', 'success')
            ->select('products.name', DB::raw('SUM(transaction_details.quantity) as total'))
            ->groupBy('products.name')
            ->orderBy('products.name')
            ->get();

        return [
            'labels' => $products->pluck('name')->toArray(),
            'datasets' => [
                [
                    'label' => 'Jumlah Terjual',
                    'data' => $products->pluck('total')->toArray(),
                    'backgroundColor' => '#f97316',
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
            'xl' => 1, // Sama seperti donut chart agar bisa tampil di samping
        ];
    }
    
}
