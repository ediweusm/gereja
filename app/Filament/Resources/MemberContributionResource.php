<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberContributionResource\Pages;
use App\Models\MemberContribution;
use App\Models\DataDictionary;
use App\Models\Account;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class MemberContributionResource extends Resource
{
    protected static ?string $model = MemberContribution::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationGroup = 'Manajemen Keuangan';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Penerimaan Jemaat';

    public static function getModelLabel(): string
    {
        return 'Penerimaan';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Penerimaan Jemaat';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data Penerimaan Jemaat')
                    ->description('Masukkan tanggal penerimaan, nama jemaat, jenis penerimaan, dan jumlah kontribusi.')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('transaction_date')
                            ->label('Tanggal Transaksi')
                            ->required()
                            ->default(now()),
                        
                        Select::make('member_id')
                            ->label('Nama Jemaat')
                            ->relationship('member', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->fullName)
                            ->searchable(['first_name', 'middle_name', 'last_name'])
                            ->preload()
                            ->required(),
                        
                        Select::make('contribution_type_id')
                            ->label('Jenis Penerimaan')
                            ->relationship(
                                name: 'contributionType',
                                titleAttribute: 'label',
                                modifyQueryUsing: fn (Builder $query) => $query->where('category', 'contribution_type')->where('is_active', true)
                            )
                            ->preload()
                            ->required(),
                        
                        TextInput::make('amount')
                            ->label('Jumlah Uang')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                    ]),

                Section::make('Akun & Keterangan Jurnal (Double-Entry)')
                    ->description('Tentukan akun kas pendebitan, akun pendapatan pengkreditan, dan uraian transaksi jurnal.')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('cash_account_id')
                            ->label('Masuk ke Kas/Bank (Debit)')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->options(fn () => \App\Models\Account::where('type', 'Asset')
                                ->where('is_active', true)
                                ->whereDoesntHave('children')
                                ->get()
                                ->pluck('full_name', 'id')
                            ),

                        Forms\Components\Select::make('revenue_account_id')
                            ->label('Akun Pendapatan (Kredit)')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->options(fn () => \App\Models\Account::where('type', 'Revenue')
                                ->where('is_active', true)
                                ->whereDoesntHave('children')
                                ->get()
                                ->pluck('full_name', 'id')
                            ),

                        Textarea::make('description')
                            ->label('Keterangan untuk Jurnal')
                            ->placeholder('Contoh: Penerimaan Persepuluhan dari Bp. Yohanis Ndun untuk bulan Juni 2026')
                            ->required()
                            ->columnSpanFull(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('member')
                    ->label('Nama Jemaat')
                    ->getStateUsing(fn ($record) => $record->member?->fullName ?? '-')
                    ->searchable(query: function (Builder $query, string $search) {
                        return $query->whereHas('member', function ($q) use ($search) {
                            $q->where('first_name', 'like', "%{$search}%")
                              ->orWhere('middle_name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%");
                        });
                    }),

                TextColumn::make('contributionType.label')
                    ->label('Jenis Penerimaan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Jumlah Uang')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 2, ',', '.'))
                    ->sortable(),

                TextColumn::make('journal.transaction_number')
                    ->label('No. Jurnal')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Tanggal Input')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('print')
                    ->label('Cetak Nota')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn (MemberContribution $record) => route('contribution.receipt', $record))
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMemberContributions::route('/'),
            'create' => Pages\CreateMemberContribution::route('/create'),
            'edit' => Pages\EditMemberContribution::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['member', 'contributionType', 'journal']);
    }
}
