<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PastorResource\Pages;
use App\Filament\Resources\PastorResource\RelationManagers;
use App\Models\Pastor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ToggleColumn;

class PastorResource extends Resource
{
    protected static ?string $model = Pastor::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    protected static ?string $navigationGroup = 'Pengaturan dan Master Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nama Lengkap (beserta gelar)')
                    ->required()
                    ->maxLength(255),
                TextInput::make('role')
                    ->label('Jabatan / Peran')
                    ->required()
                    ->default('Pendeta Jemaat')
                    ->maxLength(255),
                FileUpload::make('image_path')
                    ->label('Foto Profil')
                    ->image()
                    ->avatar()
                    ->directory('pastors')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Status Aktif Melayani')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Foto')
                    ->circular(),
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('role')
                    ->label('Jabatan')
                    ->searchable(),
                ToggleColumn::make('is_active')
                    ->label('Aktif'),
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
            'index' => Pages\ListPastors::route('/'),
            'create' => Pages\CreatePastor::route('/create'),
            'edit' => Pages\EditPastor::route('/{record}/edit'),
        ];
    }
}
