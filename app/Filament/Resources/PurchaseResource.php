<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseResource\Pages;
use App\Models\Products;
use App\Models\Purchase;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;

    protected static ?string $navigationLabel = 'Pembelian';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Manajemen Produk';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Card::make()->schema([
                Select::make('supplier_id')
                    ->label('Suplier')
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->required(),

                DatePicker::make('purchase_date')
                    ->label('Tanggal Pembelian')
                    ->default(today())
                    ->required(),

                Repeater::make('details')
                    ->label('Barang yang Dibeli')
                    ->relationship()
                    ->live() // ⬅️ Penting: agar reaktif
                    ->schema([
                        Select::make('product_id')
                            ->label('Produk')
                            ->options(Products::all()->pluck('name', 'id'))
                            ->searchable()
                            ->required(),

                        TextInput::make('buy_price')
                            ->label('Harga Beli')
                            ->numeric()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                $set('subtotal', (int) $get('buy_price') * (int) $get('quantity'));
                            }),

                        TextInput::make('quantity')
                            ->label('Jumlah')
                            ->numeric()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                $set('subtotal', (int) $get('buy_price') * (int) $get('quantity'));
                            }),

                        TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->readOnly()
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(4)
                    ->required()
                    ->afterStateUpdated(function (callable $set, $get) {
                        $details = collect($get('details') ?? []);
                        $total = $details->sum(function ($item) {
                            return (int) ($item['buy_price'] ?? 0) * (int) ($item['quantity'] ?? 0);
                        });
                        $set('total_price', $total);
                    }),

                TextInput::make('total_price')
                    ->label('Total Harga')
                    ->numeric()
                    ->readOnly()
                    ->default(0)
                    ->dehydrated()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $get) {
                        $details = collect($get('details') ?? []);
                        $total = $details->sum(function ($item) {
                            return (int) ($item['buy_price'] ?? 0) * (int) ($item['quantity'] ?? 0);
                        });
                        $set('total_price', $total);
                    }),
            ])
        ]);
    }

    public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('supplier.name')
                ->label('Suplier')
                ->searchable(), // ✅ aktifkan pencarian nama suplier

            TextColumn::make('purchase_date')
                ->label('Tanggal')
                ->date(),

            TextColumn::make('total_price')
                ->label('Total')
                ->money('IDR', true),

            TextColumn::make('created_at')
                ->label('Waktu')
                ->dateTime('d M Y'),
        ])
        ->filters([
            // ✅ Filter berdasarkan tanggal range
            Tables\Filters\Filter::make('purchase_date')
                ->form([
                    DatePicker::make('from')->label('Dari Tanggal'),
                    DatePicker::make('until')->label('Sampai Tanggal'),
                ])
                ->query(function ($query, array $data) {
                    return $query
                        ->when($data['from'], fn($q) => $q->whereDate('purchase_date', '>=', $data['from']))
                        ->when($data['until'], fn($q) => $q->whereDate('purchase_date', '<=', $data['until']));
                }),

            // ✅ Filter berdasarkan suplier
            Tables\Filters\SelectFilter::make('supplier_id')
                ->label('Filter Suplier')
                ->relationship('supplier', 'name'),
        ])
        ->actions([
            Action::make('detail')
                ->label('Detail')
                ->icon('heroicon-o-eye')
                ->modalHeading('Detail Pembelian')
                ->modalWidth('3xl')
                ->modalSubmitAction(false)
                ->infolist(function ($record) {
                    return [
                        Section::make('Informasi Pembelian')
                            ->schema([
                                TextEntry::make('supplier.name')->label('Nama Suplier'),
                                TextEntry::make('purchase_date')->label('Tanggal')->date(),
                                TextEntry::make('total_price')->label('Total')->money('IDR'),
                            ])
                            ->columns(2),

                        Section::make('Barang yang Dibeli')
                            ->schema([
                                RepeatableEntry::make('details')
                                    ->label('Produk')
                                    ->schema([
                                        TextEntry::make('product.name')->label('Nama Produk'),
                                        TextEntry::make('buy_price')->label('Harga Beli')->money('IDR'),
                                        TextEntry::make('quantity')->label('Jumlah'),
                                        TextEntry::make('subtotal')->label('Subtotal')->money('IDR'),
                                    ])
                                    ->columns(4),
                            ]),
                    ];
                }),

          
        ])
        ->defaultSort('purchase_date', 'desc');
}


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchases::route('/'),
            'create' => Pages\CreatePurchase::route('/create'),
        ];
    }
}
