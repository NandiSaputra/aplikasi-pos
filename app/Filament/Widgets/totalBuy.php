<?php

namespace App\Filament\Widgets;

use App\Models\Purchase;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class totalBuy extends ChartWidget
{
    use InteractsWithPageFilters;
    protected static ?string $heading = '📦 Total Pembelian Suplier';
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;
        $range = $this->filters['range'] ?? null;

        $query = Purchase::query();

        // Apply filter tanggal
        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('purchase_date', [$start, $end]);
        } elseif ($range) {
            $query = $this->applyRangeFilter($query, $range, 'purchase_date');
        }

        $labels = [];
        $data = [];

        // DAILY
        if ($range === 'daily') {
            $purchases = $query
                ->select(DB::raw('HOUR(purchase_date) as hour'), DB::raw('SUM(total_price) as total'))
                ->groupBy(DB::raw('HOUR(purchase_date)'))
                ->orderBy('hour')
                ->get();

            for ($i = 0; $i < 24; $i++) {
                $labels[] = $i . ':00';
                $data[] = (float) $purchases->firstWhere('hour', $i)?->total ?? 0;
            }

        // WEEKLY
        } elseif ($range === 'weekly') {
            $purchases = $query
                ->select(DB::raw('DATE(purchase_date) as date'), DB::raw('SUM(total_price) as total'))
                ->groupBy(DB::raw('DATE(purchase_date)'))
                ->orderBy('date')
                ->get();

            $weekStart = now()->startOfWeek();
            for ($i = 0; $i < 7; $i++) {
                $date = $weekStart->copy()->addDays($i)->toDateString();
                $labels[] = Carbon::parse($date)->translatedFormat('D');
                $data[] = (float) $purchases->firstWhere('date', $date)?->total ?? 0;
            }

        // MONTHLY
        } elseif ($range === 'monthly') {
            $startOfMonth = now()->startOfMonth();
            $endOfMonth = now()->endOfMonth();

            $purchases = (clone $query)
                ->select(DB::raw('DATE(purchase_date) as date'), DB::raw('SUM(total_price) as total'))
                ->whereBetween('purchase_date', [$startOfMonth, $endOfMonth])
                ->groupBy(DB::raw('DATE(purchase_date)'))
                ->orderBy('date')
                ->get();

            $period = \Carbon\CarbonPeriod::create($startOfMonth, $endOfMonth);
            foreach ($period as $date) {
                $labels[] = $date->translatedFormat('j M');
                $data[] = (float) $purchases->firstWhere('date', $date->toDateString())?->total ?? 0;
            }

        // YEARLY
        } else {
            $purchases = $query
                ->select(DB::raw('MONTH(purchase_date) as month'), DB::raw('SUM(total_price) as total'))
                ->groupBy(DB::raw('MONTH(purchase_date)'))
                ->orderBy('month')
                ->get();

            for ($i = 1; $i <= 12; $i++) {
                $labels[] = Carbon::create()->month($i)->locale('id')->translatedFormat('F');
                $data[] = (float) $purchases->firstWhere('month', $i)?->total ?? 0;
            }
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total Pembelian',
                    'data' => $data,
                    'borderColor' => '#f97316',
                    'backgroundColor' => 'rgba(251, 146, 60, 0.3)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function applyRangeFilter($query, string $range, string $column = 'purchase_date')
    {
        return match ($range) {
            'daily' => $query->whereDate($column, today()),
            'weekly' => $query->whereBetween($column, [now()->startOfWeek(), now()->endOfWeek()]),
            'monthly' => $query->whereMonth($column, now()->month)->whereYear($column, now()->year),
            'yearly' => $query->whereYear($column, now()->year),
            default => $query,
        };
    }
}
