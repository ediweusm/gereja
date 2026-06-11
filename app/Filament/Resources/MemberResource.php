<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberResource\Pages;
use App\Models\Member;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Fieldset;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Administrasi Jemaat';
    protected static ?string $navigationLabel = 'Daftar Jemaat';
    protected static ?string $recordTitleAttribute = 'first_name';
    protected static ?string $modelLabel = 'Dafta Jemaat';
    protected static ?string $pluralModelLabel = 'Daftar Jemaat';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Data Jemaat')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Data Pribadi')
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('first_name')
                                            ->label('Nama Depan')
                                            ->required(),
                                        Forms\Components\TextInput::make('middle_name')
                                            ->label('Nama Tengah'),
                                        Forms\Components\TextInput::make('last_name')
                                            ->label('Nama Belakang'),
                                    ]),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('nik')
                                            ->label('NIK'),
                                        Forms\Components\Select::make('gender')
                                            ->label('Jenis Kelamin')
                                            ->options([
                                                'L' => 'Laki-laki',
                                                'P' => 'Perempuan',
                                            ])
                                            ->required(),
                                    ]),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('birth_place')
                                            ->label('Tempat Lahir'),
                                        Forms\Components\DatePicker::make('birth_date')
                                            ->label('Tanggal Lahir'),
                                    ]),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('phone')
                                            ->label('Telepon'),
                                        Forms\Components\Toggle::make('is_deceased')
                                            ->label('Meninggal Dunia')
                                            ->live(),
                                    ]),
                                Forms\Components\DatePicker::make('death_date')
                                    ->label('Tanggal Kematian')
                                    ->visible(fn (Forms\Get $get) => (bool)$get('is_deceased')),
                            ]),
                        Tabs\Tab::make('Keluarga & Pekerjaan')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('family_id')
                                            ->label('No. KK')
                                            ->relationship('family', 'family_number')
                                            ->searchable()
                                            ->preload(),
                                        Forms\Components\Select::make('family_position_id')
                                            ->label('SHDK')
                                            ->relationship('familyPosition', 'label', fn (Builder $query) => $query->category('family_position')->active())
                                            ->searchable()
                                            ->preload(),
                                    ]),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('father_name')
                                            ->label('Nama Ayah'),
                                        Forms\Components\TextInput::make('mother_name')
                                            ->label('Nama Ibu'),
                                    ]),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('marital_status_id')
                                            ->label('Status Pernikahan')
                                            ->relationship('maritalStatus', 'label', fn (Builder $query) => $query->category('marital_status')->active())
                                            ->searchable()
                                            ->preload(),
                                        Forms\Components\Select::make('education_id')
                                            ->label('Pendidikan')
                                            ->relationship('education', 'label', fn (Builder $query) => $query->category('education')->active())
                                            ->searchable()
                                            ->preload(),
                                    ]),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('occupation_id')
                                            ->label('Pekerjaan')
                                            ->relationship('occupation', 'label', fn (Builder $query) => $query->category('occupation')->active())
                                            ->searchable()
                                            ->preload(),
                                        Forms\Components\TextInput::make('income')
                                            ->label('Penghasilan')
                                            ->numeric(),
                                    ]),
                            ]),
                        Tabs\Tab::make('Data Gerejawi')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('membership_status_id')
                                            ->label('Status Keanggotaan')
                                            ->relationship('membershipStatus', 'label', fn (Builder $query) => $query->category('membership_status')->active())
                                            ->searchable()
                                            ->preload(),
                                        Forms\Components\Select::make('church_role_id')
                                            ->label('Jabatan Gerejawi')
                                            ->relationship('churchRole', 'label', fn (Builder $query) => $query->category('church_role')->active())
                                            ->searchable()
                                            ->preload(),
                                    ]),
                                Forms\Components\Toggle::make('status_baptis')
                                    ->label('Sudah Baptis')
                                    ->live(),
                            ]),
                        Tabs\Tab::make('Sakramen & Pernikahan')
                            ->schema([
                                Fieldset::make('Baptis')
                                    ->schema([
                                        Forms\Components\DatePicker::make('baptism_date')
                                            ->label('Tanggal Baptis'),
                                        Forms\Components\TextInput::make('baptism_church')
                                            ->label('Gereja Baptis'),
                                        Forms\Components\TextInput::make('baptism_pastor')
                                            ->label('Pendeta Baptis'),
                                    ])
                                    ->columns(3)
                                    ->visible(fn (Forms\Get $get) => (bool)$get('status_baptis')),
                                Fieldset::make('Sidi')
                                    ->schema([
                                        Forms\Components\DatePicker::make('sidi_date')
                                            ->label('Tanggal Sidi'),
                                        Forms\Components\TextInput::make('sidi_church')
                                            ->label('Gereja Sidi'),
                                        Forms\Components\TextInput::make('sidi_pastor')
                                            ->label('Pendeta Sidi'),
                                    ])
                                    ->columns(3),
                                Fieldset::make('Pernikahan')
                                    ->schema([
                                        Forms\Components\DatePicker::make('marriage_date')
                                            ->label('Tanggal Pernikahan'),
                                        Forms\Components\TextInput::make('marriage_church')
                                            ->label('Gereja Pernikahan'),
                                        Forms\Components\TextInput::make('marriage_pastor')
                                            ->label('Pendeta Pernikahan'),
                                    ])
                                    ->columns(3),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('Nama Lengkap')
                    ->getStateUsing(fn (Member $record): string => $record->full_name)
                    ->searchable(['first_name', 'middle_name', 'last_name'])
                    ->sortable(['first_name'])
                    ->weight('bold'),
                TextColumn::make('gender')
                    ->label('L/P')
                    ->badge()
                    ->color(fn ($state) => $state === 'L' ? 'info' : 'success'),
                TextColumn::make('birth_date')
                    ->label('Usia')
                    ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->age . ' thn' : '-')
                    ->sortable(),
                TextColumn::make('family.family_number')
                    ->label('No. KK')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('familyPosition.label')
                    ->label('SHDK')
                    ->searchable(),
                TextColumn::make('membershipStatus.label')
                    ->label('Status')
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('gender')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ]),
                SelectFilter::make('membership_status_id')
                    ->relationship('membershipStatus', 'label', fn (Builder $query) => $query->category('membership_status')->active())
                    ->label('Status Jemaat'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // Read-only: no bulk delete or other modifying bulk actions
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
            'index' => Pages\ListMembers::route('/'),
            'view' => Pages\ViewMember::route('/{record}'),
        ];
    }
}
