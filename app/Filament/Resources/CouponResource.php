<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages;
use App\Filament\Resources\CouponResource\RelationManagers;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('code')
                ->required()
                ->unique(ignoreRecord: true)
                ->label('Kode Kupon'),
    
            Select::make('type')
                ->options([
                    'percentage' => 'Persentase (%)',
                    'fixed' => 'Potongan Tetap (Rp)',
                ])
                ->required()
                ->label('Tipe Diskon'),
    
            TextInput::make('value')
                ->numeric()
                ->required()
                ->label('Nilai Diskon'),
    
            TextInput::make('min_purchase')
                ->numeric()
                ->label('Minimal Pembelian (opsional)'),
    
            TextInput::make('usage_limit')
                ->numeric()
                ->label('Batas Penggunaan (opsional)'),
    
            TextInput::make('used_count')
                ->numeric()
                ->disabled()
                ->label('Sudah Digunakan'),
    
            DatePicker::make('expired_at')
                ->label('Tanggal Kadaluarsa'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('code')->searchable(),
            TextColumn::make('type'),
            TextColumn::make('value'),
            TextColumn::make('min_purchase'),
            TextColumn::make('usage_limit'),
            TextColumn::make('used_count'),
            TextColumn::make('expired_at')->date(),
        ])
        ->defaultSort('expired_at', 'asc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
