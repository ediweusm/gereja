<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RayonResource\Pages;
use App\Models\Rayon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class RayonResource extends Resource
{
    protected static ?string $model = Rayon::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationGroup = 'Pengaturan dan Master Data';

    public static function getModelLabel(): string
    {
        return 'Rayon';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Rayon';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nama Rayon')
                    ->required()
                    ->maxLength(100),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->nullable()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Rayon')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50),
                TextColumn::make('families_count')
                    ->counts('families')
                    ->label('Jumlah KK'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListRayons::route('/'),
            'create' => Pages\CreateRayon::route('/create'),
            'edit' => Pages\EditRayon::route('/{record}/edit'),
        ];
    }
}
