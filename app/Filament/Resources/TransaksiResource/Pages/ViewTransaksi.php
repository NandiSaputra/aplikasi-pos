<?php

namespace App\Filament\Resources\TransaksiResource\Pages;

use App\Filament\Resources\TransaksiResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;

class ViewTransaksi extends ViewRecord
{
    protected static string $resource = TransaksiResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    
    protected function getInfolistSchema(): array
    {
        return [
            Section::make('Detail Transaksi')
                ->schema([
                    TextEntry::make('invoice_number')->label('Invoice'),
                    TextEntry::make('user.name')->label('Kasir'),
                    TextEntry::make('payment_method')->label('Metode Pembayaran'),
                    TextEntry::make('payment_status')->label('Status Pembayaran'),
                    TextEntry::make('total_price')->label('Total Harga')->money('IDR'),
                    TextEntry::make('created_at')->label('Waktu Transaksi')->dateTime(),
                ])
                ->columns(2),

            Section::make('Produk Terjual')
                ->schema([
                    RepeatableEntry::make('details')
                        ->label('Daftar Produk')
                        ->schema([
                            TextEntry::make('product.name')->label('Nama Produk'),
                            TextEntry::make('quantity')->label('Jumlah'),
                            TextEntry::make('price')->label('Harga Satuan')->money('IDR'),
                            TextEntry::make('subtotal')->label('Subtotal')->money('IDR'),
                        ])
                        ->columns(4),
                ]),
        ];
    }
}
