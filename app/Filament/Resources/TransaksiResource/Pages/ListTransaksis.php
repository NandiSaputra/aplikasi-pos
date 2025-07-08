<?php

namespace App\Filament\Resources\TransaksiResource\Pages;

use App\Exports\TransaksiExport;
use App\Filament\Resources\TransaksiResource;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\ExportAction\Exports\FromTable;
use Maatwebsite\Excel\Facades\Excel;

class ListTransaksis extends ListRecords
{
    protected static string $resource = TransaksiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('Export PDF')
            ->icon('heroicon-o-arrow-down-tray')
            ->button()
            ->color('success')
            ->action(function ($livewire) {
                // Ambil query tabel yang sedang difilter
                $query = $livewire->getFilteredTableQuery();
                $transaksis = $query->with(['details', 'user'])->get();
                if ($transaksis->count() > 1000) {
                    Notification::make()
                        ->title('Terlalu banyak data untuk diekspor!')
                        ->danger()
                        ->body('Silakan gunakan filter untuk mengurangi data atau export menggunakan excel.')
                        ->send();
                
                    return;
                }
                

                // Hitung total
                $totalDiskonKupon = $transaksis->sum(fn($trx) => floatval($trx->discount_amount));
                $totalDiskonProduk = $transaksis->sum(fn($trx) => floatval($trx->product_discount_total));
                $totalPenjualanKotor = $transaksis->sum(fn($trx) => $trx->details->sum(fn($d) => $d->price * $d->quantity));
                $totalPendapatan = $totalPenjualanKotor - $totalDiskonKupon - $totalDiskonProduk;
                $totalModal = $transaksis->sum(fn($trx) => $trx->details->sum(fn($d) => $d->buy_price * $d->quantity));
                $labaBersih = $totalPendapatan - $totalModal;

                // Generate PDF
                $pdf = Pdf::loadView('exports.transaksi_pdf', [
                    'transaksis' => $transaksis,
                    'totalDiskonKupon' => $totalDiskonKupon,
                    'totalDiskonProduk' => $totalDiskonProduk,
                    'totalPenjualanKotor' => $totalPenjualanKotor,
                    'totalPendapatan' => $totalPendapatan,
                    'totalModal' => $totalModal,
                    'labaBersih' => $labaBersih,
                ]);

                // Kirim response download
                return response()->streamDownload(
                    fn () => print($pdf->stream()),
                    'laporan-transaksi.pdf'
                );
            }),
            Action::make('export')
            ->label('Export Excel')
            ->icon('heroicon-o-arrow-down-tray')
            ->action(function () {
                $transaksis = $this->getFilteredTableQuery()->latest()->get();

                return Excel::download(
                    new TransaksiExport($transaksis),
                    'transaksi-' . now()->format('Ymd_His') . '.xlsx'
                );
            }),
         
        ];
    }
}