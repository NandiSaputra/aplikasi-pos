<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductsResource\Pages;
use App\Models\Products;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

class ProductsResource extends Resource
{
    protected static ?string $model = Products::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';
    protected static ?string $navigationLabel = 'Produk';
    protected static ?string $pluralLabel = 'Produk';
    protected static ?string $navigationGroup = 'Manajemen Produk';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nama Produk')
                    ->required()
                    ->maxLength(255),

                Select::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                    

                    TextInput::make('buy_price')
                    ->label('Harga Beli')
                    ->numeric()
                    ->readOnly()
                    ->disabled()
                    ->dehydrated(false) // ← agar tidak ikut terkirim saat submit
                    ->helperText('Harga beli diperbarui otomatis dari pembelian.')
                    ->default(0),

                TextInput::make('price')
                    ->label('Harga Jual termasuk ppn 11%')
                    ->numeric()
                    ->required()
                    ->default(0),

                    TextInput::make('stock')
    ->label('Stok')
    ->numeric()
    ->readOnly()
    ->disabled()
    ->dehydrated(false)
    ->helperText('Stok diperbarui otomatis saat pembelian atau penjualan.')
    ->default(0),

             

                FileUpload::make('image')
                    ->label('Gambar')
                    ->image()
                    ->disk('public')
                    ->directory('produk')
                    ->previewable(true)
                    ->imagePreviewHeight(150)
                    ->openable()
                    ->downloadable()
                    ->filled() // untuk edit tetap muncul
                    ->preserveFilenames()
                    ->maxSize(2048)
                    ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                    ->visibility('public')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Foto')
                    ->disk('public')
                    ->width(80)
                    ->height(60),

                TextColumn::make('name')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('buy_price')
                    ->label('Harga Beli')
                    ->money('IDR', true)
                    ->sortable(),

                TextColumn::make('price')
                    ->label('Harga Jual termasuk ppn 11%')
                    ->money('IDR', true)
                    ->sortable(),

                TextColumn::make('stock')
                    ->label('Stok')
                    ->sortable(),

              
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProducts::route('/create'),
            'edit' => Pages\EditProducts::route('/{record}/edit'),
        ];
    }
}
