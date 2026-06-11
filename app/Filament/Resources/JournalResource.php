<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JournalResource\Pages;
use App\Models\Journal;
use App\Models\Account;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class JournalResource extends Resource
{
    protected static ?string $model = Journal::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';

    protected static ?string $navigationGroup = 'Manajemen Keuangan';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Jurnal Umum';

    public static function getModelLabel(): string
    {
        return 'Jurnal';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Jurnal';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Header Jurnal')
                    ->description('Masukkan tanggal transaksi, nomor bukti, dan keterangan.')
                    ->schema([
                        DatePicker::make('transaction_date')
                            ->label('Tanggal Transaksi')
                            ->required()
                            ->default(now()),
                        TextInput::make('reference_number')
                            ->label('Nomor Bukti/Nota')
                            ->maxLength(100)
                            ->nullable(),
                        Textarea::make('description')
                            ->label('Keterangan Transaksi')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('Detail Transaksi / Double-Entry')
                    ->description('Masukkan minimal 2 akun dengan nilai debit dan kredit yang seimbang (balance).')
                    ->schema([
                        Repeater::make('items')
                            ->relationship('items')
                            ->minItems(2)
                            ->columns(3)
                            ->rules([
                                fn () => function (string $attribute, $value, \Closure $fail) {
                                    $debitSum = collect($value)->sum(fn ($item) => (float) ($item['debit'] ?? 0));
                                    $creditSum = collect($value)->sum(fn ($item) => (float) ($item['credit'] ?? 0));

                                    if (round($debitSum, 2) !== round($creditSum, 2)) {
                                        $fail('Total Debit dan Kredit tidak seimbang (tidak balance). Total Debit: Rp ' . number_format($debitSum, 2, ',', '.') . ', Total Kredit: Rp ' . number_format($creditSum, 2, ',', '.'));
                                    }
                                }
                            ])
                            ->schema([
                                Select::make('account_id')
                                    ->label('Akun / Rekening')
                                    ->relationship(
                                        name: 'account',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn (Builder $query) => $query->whereDoesntHave('children')->where('is_active', true)
                                    )
                                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->code} - {$record->name}")
                                    ->searchable(['code', 'name'])
                                    ->preload()
                                    ->required()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                                TextInput::make('debit')
                                    ->label('Debit')
                                    ->numeric()
                                    ->default(0)
                                    ->required(),
                                TextInput::make('credit')
                                    ->label('Kredit')
                                    ->numeric()
                                    ->default(0)
                                    ->required(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction_number')
                    ->label('Nomor Transaksi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('transaction_date')
                    ->label('Tanggal Transaksi')
                    ->date()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Keterangan')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('total_nominal')
                    ->label('Total Transaksi')
                    ->state(fn (Journal $record) => $record->total_nominal)
                    ->money('IDR', locale: 'id_ID'),
            ])
            ->filters([
                Filter::make('transaction_date')
                    ->form([
                        DatePicker::make('from')
                            ->label('Dari Tanggal'),
                        DatePicker::make('until')
                            ->label('Hingga Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('transaction_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('transaction_date', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('print')
                    ->label('Cetak Bukti')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn (\App\Models\Journal $record) => route('journal.print', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('kwitansi')
                    ->label('Cetak Kwitansi')
                    ->icon('heroicon-o-ticket')
                    ->color('warning')
                    ->url(fn (\App\Models\Journal $record) => route('journal.kwitansi', $record))
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
            'index' => Pages\ListJournals::route('/'),
            'create' => Pages\CreateJournal::route('/create'),
            'edit' => Pages\EditJournal::route('/{record}/edit'),
        ];
    }
}
