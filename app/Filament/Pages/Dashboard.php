<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard as BaseDashboard;

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
                                'daily' => 'Harian',
                                'weekly' => 'Mingguan',
                                'monthly' => 'Bulanan',
                                'yearly' => 'Tahunan',
                            ])
                            ->default('monthly'),

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
