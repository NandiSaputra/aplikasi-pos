<?php

namespace App\Filament\Resources\PurchaseResource\Pages;

use App\Filament\Resources\PurchaseResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListPurchases extends ListRecords
{
    protected static string $resource = PurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('Export Excel')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('success')
            ->action(function ($livewire) {
                $query = $livewire->getFilteredTableQuery();
                $purchases = $query->with('supplier')->get();
                $totalSemua = $purchases->sum('total_price');
        
                return \Maatwebsite\Excel\Facades\Excel::download(
                    new \App\Exports\PurchaseExport($purchases, $totalSemua),
                    'laporan-pembelian.xlsx'
                );
            }),
        

Action::make('Export PDF')
    ->icon('heroicon-o-document-text')
    ->color('danger')
    ->action(function ($livewire) {
        $query = $livewire->getFilteredTableQuery();
        $purchases = $query->with('supplier')->get();
        if ($purchases->count() > 1000) {
            Notification::make()
                ->title('Terlalu banyak data untuk diekspor!')
                ->danger()
                ->body('Silakan gunakan filter untuk mengurangi data atau export menggunakan excel.')
                ->send();
        
            return;
        }
        $totalSemua = $purchases->sum('total_price');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.purchase-pdf', [
            'purchases' => $purchases,
            'totalSemua' => $totalSemua,
        ]);

        return response()->streamDownload(
            fn () => print($pdf->stream()),
            'laporan-pembelian.pdf'
        );
    }),

        ];
    }
}
