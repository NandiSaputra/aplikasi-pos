<?php

namespace App\Filament\Widgets;

use App\Models\Transaksi;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class totalSales extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = '📈 Total Penjualan per Bulan';
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $startDate = $this->filters['startDate'] ?? now()->startOfYear();
        $endDate = $this->filters['endDate'] ?? now()->endOfYear();
        $range = $this->filters['range'] ?? null;

        $query = Transaksi::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_price) as total')
            )
            ->where('payment_status', 'success')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month');

        $sales = $query->get();

        $labels = [];
        $data = [];

        // Inisialisasi 12 bulan
        for ($i = 1; $i <= 12; $i++) {
            $labels[] = Carbon::create()->month($i)->locale('id')->translatedFormat('F');
            $data[] = (float) $sales->firstWhere('month', $i)?->total ?? 0;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total Penjualan',
                    'data' => $data,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                    'fill' => true,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line'; // Bisa diganti ke 'bar' kalau mau chart batang
    }

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }
}
