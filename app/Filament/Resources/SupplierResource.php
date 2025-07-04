<?php
namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Models\Suplier;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class SupplierResource extends Resource
{
    protected static ?string $model = Suplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Suplier';
    protected static ?string $pluralLabel = 'Suplier';
    protected static ?string $navigationGroup = 'Manajemen Pembelian';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('Nama Suplier')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),

            TextInput::make('phone')
                ->label('No. Telepon')
                ->maxLength(20),

            

            TextInput::make('address')
                ->label('Alamat')
                ->maxLength(500)
                ->nullable(),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Nama'),
            TextColumn::make('phone')->label('Telepon'),
          
            TextColumn::make('address')->label('Alamat')->wrap(),
            TextColumn::make('created_at')->dateTime('d M Y')->label('Dibuat'),
        ])
        ->searchable()
        ->defaultSort('created_at', 'desc')
        ->filters([])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
