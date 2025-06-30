<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductsResource\Pages;
use App\Filament\Resources\ProductsResource\RelationManagers;
use App\Models\Products;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductsResource extends Resource
{
    protected static ?string $model = Products::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->label('Name')->required(),
                Select::make('category_id')->label('Category')->relationship('category', 'name')->required()->searchable()
                ->preload(),
                TextInput::make('price')->label('Price')->required(),
                TextInput::make('stock')->label('Stock')->required(),
                TextInput::make('discount')
                    ->label('Diskon (%)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->default(0)
                    ->suffix('%')
                    ->helperText('Masukkan diskon dalam persen, contoh: 10 untuk diskon 10%')
                    ->required(),

                FileUpload::make('image')
                    ->label('Image')
                    ->disk('public')
                    ->directory('produk')
                    ->image()
                    ->previewable(true)
                    ->imagePreviewHeight(150)
                    ->openable()
                    ->downloadable()
                    ->columnSpanFull()
                    ->filled() // ini memastikan data tidak hilang saat edit
                    ->preserveFilenames()
                    ->maxSize(2048)
                    ->helperText('Upload cover image (JPG, PNG)')
                    ->visibility('public')
                    ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('category.name')->sortable()->searchable(),
                TextColumn::make('price')->sortable()->searchable(),
                TextColumn::make('stock')->sortable()->searchable(),
                TextColumn::make('discount')
                    ->label('Diskon')
                    ->formatStateUsing(fn ($state) => $state ? $state . '%' : '-')
                    ->sortable(),

                ImageColumn::make('image')->label('Image') ->disk('public')->width(100)->height(60),            // opsional: atur ukuran gambar

            
            ])
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProducts::route('/create'),
            'edit' => Pages\EditProducts::route('/{record}/edit'),
        ];
    }
}
