<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SliderResource\Pages;
use App\Filament\Resources\SliderResource\RelationManagers;
use App\Models\Slider;
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

class SliderResource extends Resource
{
    protected static ?string $model = Slider::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Manajemen Konten';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->label('Judul / Keterangan Singkat (Opsional)')
                    ->maxLength(255),
                FileUpload::make('image_path')
                    ->label('Gambar Slider (Landscape)')
                    ->image()
                    ->directory('sliders')
                    ->required()
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Tampilkan di Beranda')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Gambar Banner')
                    ->disk('public')
                    ->width(150)
                    ->height(50)
                    ->square(), // Menampilkan gambar dalam bentuk kotak/landscape
                    
                TextColumn::make('title')
                    ->label('Judul / Keterangan')
                    ->searchable()
                    ->placeholder('Tanpa Judul'), // Teks pengganti jika judul dikosongkan
                    
                ToggleColumn::make('is_active')
                    ->label('Tampil di Beranda'),
                    
                TextColumn::make('order')
                    ->label('Urutan')
                    ->sortable(),
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
            'index' => Pages\ListSliders::route('/'),
            'create' => Pages\CreateSlider::route('/create'),
            'edit' => Pages\EditSlider::route('/{record}/edit'),
        ];
    }
}
