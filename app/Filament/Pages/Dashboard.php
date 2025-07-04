<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\bestSeller;
use App\Filament\Widgets\payment;
use App\Filament\Widgets\product;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\totalSales;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\StatsOverviewWidget;

class Dashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;

   
    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        // Filter rentang waktu
                        Select::make('range')
                            ->label('Rentang Waktu')
                            ->options([
                                'daily' => 'Hari Ini',
                                'weekly' => 'Minggu',
                                'monthly' => 'Bulan',
                                'yearly' => 'Tahun',
                            ])
                            ->default('daily'),

                        // Tanggal mulai
                        DatePicker::make('startDate')
                            ->label('Dari Tanggal')
                            ->maxDate(fn (Get $get) => $get('endDate') ?: now()),

                        // Tanggal selesai
                        DatePicker::make('endDate')
                            ->label('Sampai Tanggal')
                            ->minDate(fn (Get $get) => $get('startDate') ?: now())
                            ->maxDate(now()),
                    ])
                    ->columns(3),
                            ]);

         
    }

}
