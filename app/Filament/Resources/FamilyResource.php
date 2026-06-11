<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FamilyResource\Pages;
use App\Filament\Resources\FamilyResource\RelationManagers;
use App\Models\Family;
use App\Models\DataDictionary;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class FamilyResource extends Resource
{
    protected static ?string $model = Family::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationGroup = 'Administrasi Jemaat';
    protected static ?int $navigationSort = 1;
    
    public static function getModelLabel(): string
    {
        return 'Kartu Keluarga';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Kartu Keluarga';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Kartu Keluarga')
                    ->description('Masukkan nomor KK, rayon, dan alamat tempat tinggal.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('family_number')
                                    ->label('Nomor KK Gereja')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(50),
                                Select::make('rayon_id')
                                    ->relationship('rayon', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->label('Rayon'),
                            ]),
                        Textarea::make('address')
                            ->label('Alamat')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('phone')
                            ->label('Telepon / HP')
                            ->maxLength(20),
                    ]),
                Section::make('Kondisi & Status Rumah')
                    ->description('Informasi kategori rumah dan status kepemilikan.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('house_category_id')
                                    ->label('Kategori Rumah')
                                    ->options(fn () => DataDictionary::active()->category('house_category')->pluck('label', 'id'))
                                    ->searchable()
                                    ->preload(),
                                Select::make('house_status_id')
                                    ->label('Status Rumah')
                                    ->options(fn () => DataDictionary::active()->category('house_status')->pluck('label', 'id'))
                                    ->searchable()
                                    ->preload(),
                            ]),
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('family_number')
                    ->label('Nomor KK Gereja')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('head_of_family')
                    ->label('Kepala Keluarga')
                    ->getStateUsing(function (Family $record): string {
                        $head = $record->members->first(fn($m) => $m->familyPosition?->code === 'suami') 
                            ?? $record->members->first();
                        return $head ? $head->fullName : '-';
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('members', function (Builder $q) use ($search) {
                            $q->where('first_name', 'like', "%{$search}%")
                              ->orWhere('middle_name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%");
                        });
                    }),
                TextColumn::make('rayon.name')
                    ->label('Rayon')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('address')
                    ->label('Alamat')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('members_count')
                    ->counts('members')
                    ->label('Jumlah Anggota'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('print')
                    ->label('Cetak KK')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn (Family $record) => route('family.print', $record))
                    ->openUrlInNewTab(),
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
            RelationManagers\MembersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFamilies::route('/'),
            'create' => Pages\CreateFamily::route('/create'),
            'edit' => Pages\EditFamily::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['members.familyPosition']);
    }
}
