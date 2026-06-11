<?php

namespace App\Filament\Pages;

use App\Models\Account;
use App\Models\JournalItem;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class GeneralLedgerReport extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected static ?string $navigationIcon  = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Manajemen Keuangan';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationLabel = 'Buku Besar';
    protected static ?string $title           = 'Buku Besar (General Ledger)';

    protected static string $view = 'filament.pages.general-ledger-report';

    // ── Filter State ──────────────────────────────────────────────────────────
    public ?int    $account_id  = null;
    public ?string $start_date  = null;
    public ?string $end_date    = null;

    public function mount(): void
    {
        $this->start_date = now()->startOfMonth()->format('Y-m-d');
        $this->end_date   = now()->endOfMonth()->format('Y-m-d');

        $this->form->fill([
            'account_id' => null,
            'start_date' => $this->start_date,
            'end_date'   => $this->end_date,
        ]);
    }

    // ── Form (Filter Atas) ────────────────────────────────────────────────────
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)->schema([
                    Select::make('account_id')
                        ->label('Pilih Akun Keuangan')
                        ->options(
                            Account::query()
                                ->active()
                                ->orderBy('code')
                                ->get()
                                ->pluck('full_name', 'id')
                        )
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated(fn () => $this->resetTable())
                        ->required(),

                    DatePicker::make('start_date')
                        ->label('Dari Tanggal')
                        ->displayFormat('d/m/Y')
                        ->native(false)
                        ->live()
                        ->afterStateUpdated(fn () => $this->resetTable())
                        ->required(),

                    DatePicker::make('end_date')
                        ->label('Sampai Tanggal')
                        ->displayFormat('d/m/Y')
                        ->native(false)
                        ->live()
                        ->afterStateUpdated(fn () => $this->resetTable())
                        ->required(),
                ]),
            ]);
    }

    // ── Table (Daftar Transaksi) ───────────────────────────────────────────────
    public function table(Table $table): Table
    {
        $startDate = $this->start_date
            ? Carbon::parse($this->start_date)->startOfDay()
            : now()->startOfMonth()->startOfDay();

        $endDate = $this->end_date
            ? Carbon::parse($this->end_date)->endOfDay()
            : now()->endOfMonth()->endOfDay();

        return $table
            ->query(
                JournalItem::query()
                    ->select('journal_items.*')
                    ->join('journals', 'journals.id', '=', 'journal_items.journal_id')
                    ->with('journal')
                    ->when(
                        $this->account_id,
                        fn (Builder $q) => $q->where('journal_items.account_id', $this->account_id)
                    )
                    ->when(
                        $this->start_date && $this->end_date,
                        fn (Builder $q) => $q->whereBetween('journals.transaction_date', [
                            $startDate,
                            $endDate,
                        ])
                    )
                    // Kosongkan tabel jika akun belum dipilih
                    ->when(
                        ! $this->account_id,
                        fn (Builder $q) => $q->whereNull('journal_items.id')
                    )
            )
            ->defaultSort('journals.transaction_date', 'asc')
            ->columns([
                TextColumn::make('journal.transaction_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('journal.transaction_number')
                    ->label('No. Bukti')
                    ->searchable()
                    ->color('gray')
                    ->size(TextColumn\TextColumnSize::Small),

                TextColumn::make('journal.description')
                    ->label('Uraian / Keterangan')
                    ->wrap()
                    ->searchable(),

                TextColumn::make('debit')
                    ->label('Debit (Rp)')
                    ->numeric(decimalPlaces: 2, decimalSeparator: ',', thousandsSeparator: '.')
                    ->alignRight()
                    ->color('success')
                    ->placeholder('-'),

                TextColumn::make('credit')
                    ->label('Kredit (Rp)')
                    ->numeric(decimalPlaces: 2, decimalSeparator: ',', thousandsSeparator: '.')
                    ->alignRight()
                    ->color('danger')
                    ->placeholder('-'),
            ])
            ->striped()
            ->paginated([25, 50, 100]);
    }

    // ── Computed Property: Banner Statistik ───────────────────────────────────
    // Accessed in Blade as: $this->reportStats
    public function getReportStatsProperty(): array
    {
        if (! $this->account_id || ! $this->start_date || ! $this->end_date) {
            return [];
        }

        $account = Account::find($this->account_id);
        if (! $account) {
            return [];
        }

        $startDate = Carbon::parse($this->start_date)->startOfDay();
        $endDate   = Carbon::parse($this->end_date)->endOfDay();

        // Tentukan apakah akun ber-saldo normal Debit (Asset, Expense)
        // atau Kredit (Liability, Net Asset, Revenue)
        $isDebitNormal = in_array($account->type, ['Asset', 'Expense']);

        // 1. Saldo Awal — semua mutasi SEBELUM tanggal mulai filter
        $openingRow = JournalItem::where('account_id', $this->account_id)
            ->whereHas(
                'journal',
                fn ($q) => $q->where('transaction_date', '<', $startDate)
            )
            ->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(credit), 0) as total_credit')
            ->first();

        $openingDebit  = (float) ($openingRow->total_debit  ?? 0);
        $openingCredit = (float) ($openingRow->total_credit ?? 0);

        $beginningBalance = $isDebitNormal
            ? ($openingDebit - $openingCredit)
            : ($openingCredit - $openingDebit);

        // 2. Mutasi Berjalan — rentang start_date s.d. end_date
        $mutationRow = JournalItem::where('account_id', $this->account_id)
            ->whereHas(
                'journal',
                fn ($q) => $q->whereBetween('transaction_date', [$startDate, $endDate])
            )
            ->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(credit), 0) as total_credit')
            ->first();

        $totalDebit  = (float) ($mutationRow->total_debit  ?? 0);
        $totalCredit = (float) ($mutationRow->total_credit ?? 0);

        // 3. Saldo Akhir
        $endingBalance = $isDebitNormal
            ? ($beginningBalance + $totalDebit - $totalCredit)
            : ($beginningBalance + $totalCredit - $totalDebit);

        return [
            'account_name'      => $account->full_name,
            'account_type'      => $account->type,
            'beginning_balance' => $beginningBalance,
            'total_debit'       => $totalDebit,
            'total_credit'      => $totalCredit,
            'ending_balance'    => $endingBalance,
        ];
    }

    // ── Helper: Format Rupiah ─────────────────────────────────────────────────
    public function formatRupiah(float $amount): string
    {
        return 'Rp ' . number_format($amount, 2, ',', '.');
    }

    // ── Helper: Format tanggal ke Bahasa Indonesia ────────────────────────────
    public function formatDateId(?string $date): string
    {
        if (! $date) {
            return '-';
        }
        static $months = [
            1 => 'Januari',   2 => 'Februari', 3 => 'Maret',    4 => 'April',
            5 => 'Mei',       6 => 'Juni',     7 => 'Juli',     8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $d = Carbon::parse($date);
        return $d->day . ' ' . $months[(int) $d->month] . ' ' . $d->year;
    }
}
