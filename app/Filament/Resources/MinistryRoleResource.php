<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MinistryRoleResource\Pages;
use App\Models\MinistryRole;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class MinistryRoleResource extends Resource
{
    protected static ?string $model = MinistryRole::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Pengaturan dan Master Data';

    public static function getModelLabel(): string
    {
        return 'Peran Pelayanan';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Peran Pelayanan';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nama Peran')
                    ->required()
                    ->maxLength(255),
                TextInput::make('sort_order')
                    ->label('Urutan Tampilan')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Peran')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable(),
            ])
            ->defaultSort('sort_order', 'asc')
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
            'index' => Pages\ListMinistryRoles::route('/'),
            'create' => Pages\CreateMinistryRole::route('/create'),
            'edit' => Pages\EditMinistryRole::route('/{record}/edit'),
        ];
    }
}
