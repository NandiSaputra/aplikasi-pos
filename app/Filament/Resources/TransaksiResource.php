<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaksiResource\Pages;
use App\Models\Transaksi;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\Action;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;

class TransaksiResource extends Resource
{
    protected static ?string $model = Transaksi::class;

    protected static ?string $navigationLabel = 'Riwayat Transaksi';
    protected static ?string $navigationGroup = 'Riwayat';
    protected static ?string $navigationIcon = 'heroicon-o-receipt-refund';

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Invoice')
                    ->searchable(), // ENABLE SEARCH
                TextColumn::make('user.name')->label('Kasir'),
                TextColumn::make('payment_method')->label('Metode'),
                TextColumn::make('total_price')->money('IDR', true),
                TextColumn::make('payment_status')->badge(),
                TextColumn::make('created_at')->label('Waktu')->dateTime('d M Y'),
            ])
            ->filters([
                // ✅ Filter status pembayaran
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'success' => 'Sukses',
                        'pending' => 'Pending',
                        'failed' => 'Gagal',
                    ]),

                // ✅ Filter berdasarkan tanggal
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('from')->label('Dari Tanggal'),
                        DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q) => $q->whereDate('created_at', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->whereDate('created_at', '<=', $data['until']));
                    })
                    ->label('Rentang Tanggal'),
            ])
            ->actions([
                // ✅ Modal detail transaksi
                Action::make('detail')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Detail Transaksi')
                    ->modalWidth('3xl')
                    ->infolist(function (Transaksi $record) {
                        return [
                            Section::make('Informasi Transaksi')
                                ->schema([
                                    TextEntry::make('invoice_number')->label('Invoice'),
                                    TextEntry::make('user.name')->label('Kasir'),
                                    TextEntry::make('payment_method')->label('Metode Pembayaran'),
                                    TextEntry::make('payment_status')->label('Status Pembayaran'),
                                    TextEntry::make('coupon_code')->label('Kode Kupon'),
                                    textEntry::make('discount_amount')->label('Diskon'),
                                    TextEntry::make('total_price')->label('Total Harga')->money('IDR'),
                                ])
                                ->columns(2),

                            Section::make('Produk Terjual')
                                ->schema([
                                    RepeatableEntry::make('details')
                                        ->label('Rincian Produk')
                                        ->schema([
                                            TextEntry::make('product.name')->label('Nama Produk'),
                                            TextEntry::make('quantity')->label('Jumlah'),
                                            TextEntry::make('price')->label('Harga Satuan')->money('IDR'),
                                            TextEntry::make('subtotal')->label('Subtotal')->money('IDR'),
                                        ])
                                        ->columns(4),
                                ]),
                        ];
                    })
                    ->modalSubmitAction(false),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransaksis::route('/'),
        ];
    }
    public static function canCreate(): bool
{
    return false;
}
}
