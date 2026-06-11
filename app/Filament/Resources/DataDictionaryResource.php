<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DataDictionaryResource\Pages;
use App\Models\DataDictionary;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;

class DataDictionaryResource extends Resource
{
    protected static ?string $model = DataDictionary::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Pengaturan dan Master Data';

    public static function getModelLabel(): string
    {
        return 'Kamus Data';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Kamus Data';
    }

    protected static array $categories = [
        'house_category' => 'Kategori Rumah',
        'house_status' => 'Status Rumah',
        'family_position' => 'Posisi Keluarga',
        'marital_status' => 'Status Pernikahan',
        'education' => 'Pendidikan',
        'occupation' => 'Pekerjaan',
        'church_role' => 'Jabatan Gereja',
        'membership_status' => 'Status Keanggotaan',
        'contribution_type' => 'Jenis Kontribusi Khusus',
        'worship_type' => 'Jenis Ibadah',
        'worship_venue' => 'Tempat Ibadah',
        'event_type' => 'Jenis Kegiatan',
        'event_mode' => 'Mode Kegiatan',
    ];

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('category')
                    ->label('Kategori')
                    ->options(self::$categories)
                    ->required(),
                TextInput::make('label')
                    ->label('Label')
                    ->required()
                    ->maxLength(100)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, ?string $state, ?string $operation) {
                        if ($operation === 'create' && filled($state)) {
                            $set('code', \Illuminate\Support\Str::slug($state));
                        }
                    }),
                TextInput::make('code')
                    ->label('Kode')
                    ->nullable()
                    ->maxLength(50)
                    ->helperText('Opsional, untuk mapping logic backend')
                    ->readOnly()
                    ->dehydrated()
                    ->unique(ignoreRecord: true),
                TextInput::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::$categories[$state] ?? $state)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('label')
                    ->label('Label')
                    ->searchable(),
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable(),
                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable(),
                ToggleColumn::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(self::$categories),
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
            'index' => Pages\ListDataDictionaries::route('/'),
            'create' => Pages\CreateDataDictionary::route('/create'),
            'edit' => Pages\EditDataDictionary::route('/{record}/edit'),
        ];
    }
}
