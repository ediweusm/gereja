<?php

namespace App\Filament\Pages;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class FinancialPositionReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-scale';
    protected static ?string $navigationGroup = 'Manajemen Keuangan';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationLabel = 'Neraca Keuangan';
    protected static ?string $title           = 'Neraca Keuangan';

    protected static string $view = 'filament.pages.financial-position-report';

    /**
     * The report date — bound directly as a component property
     * (no $data wrapper) so the computed property reacts to changes.
     */
    public ?string $as_of_date = null;

    public function mount(): void
    {
        $this->as_of_date = now()->format('Y-m-d');

        $this->form->fill([
            'as_of_date' => $this->as_of_date,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        DatePicker::make('as_of_date')
                            ->label('Per Tanggal (As of Date)')
                            ->required()
                            ->displayFormat('d/m/Y')
                            ->maxDate(now())
                            ->native(false)
                            ->live(),       // triggers Livewire update on change
                    ])
                    ->columns(3),
            ]);
            // No ->statePath('data') → Filament binds directly to $this->as_of_date
    }

    // ──────────────────────────────────────────────────────────────────────────
    // COMPUTED PROPERTY (Livewire v3 style)
    // Accessed in Blade as: $this->reportData
    // Re-evaluated automatically whenever $as_of_date changes via ->live()
    // ──────────────────────────────────────────────────────────────────────────
    public function getReportDataProperty(): array
    {
        if (blank($this->as_of_date)) {
            return [];
        }

        $date = Carbon::parse($this->as_of_date)->endOfDay();

        // ── QUERY 1: Surplus / Defisit Berjalan ─────────────────────────────
        // Aggregate Revenue & Expense up to $date
        $incomeRows = DB::table('journal_items')
            ->join('journals', 'journals.id', '=', 'journal_items.journal_id')
            ->join('accounts', 'accounts.id', '=', 'journal_items.account_id')
            ->whereIn('accounts.type', ['Revenue', 'Expense'])
            ->where('journals.transaction_date', '<=', $date)
            ->groupBy('accounts.type')
            ->select([
                'accounts.type',
                DB::raw('COALESCE(SUM(journal_items.debit),  0) as total_debit'),
                DB::raw('COALESCE(SUM(journal_items.credit), 0) as total_credit'),
            ])
            ->get()
            ->keyBy('type');

        $revenueRow = $incomeRows->get('Revenue');
        $expenseRow = $incomeRows->get('Expense');

        $totalRevenue = $revenueRow
            ? (float) $revenueRow->total_credit - (float) $revenueRow->total_debit
            : 0.0;

        $totalExpense = $expenseRow
            ? (float) $expenseRow->total_debit - (float) $expenseRow->total_credit
            : 0.0;

        $surplusDeficit = $totalRevenue - $totalExpense; // positive = surplus

        // ── QUERY 2: Balance Sheet Accounts ─────────────────────────────────
        $balanceRows = DB::table('journal_items')
            ->join('journals', 'journals.id', '=', 'journal_items.journal_id')
            ->join('accounts', 'accounts.id', '=', 'journal_items.account_id')
            ->whereIn('accounts.type', ['Asset', 'Liability', 'Net Asset'])
            ->where('journals.transaction_date', '<=', $date)
            ->where('accounts.is_active', true)
            ->groupBy('accounts.id', 'accounts.code', 'accounts.name', 'accounts.type')
            ->select([
                'accounts.id',
                'accounts.code',
                'accounts.name',
                'accounts.type',
                DB::raw('COALESCE(SUM(journal_items.debit),  0) as total_debit'),
                DB::raw('COALESCE(SUM(journal_items.credit), 0) as total_credit'),
            ])
            ->orderBy('accounts.code')
            ->get();

        $assets         = [];
        $liabilities    = [];
        $netAssets      = [];
        $totalAssets        = 0.0;
        $totalLiabilities   = 0.0;
        $totalNetAssets     = 0.0;

        foreach ($balanceRows as $row) {
            $debit  = (float) $row->total_debit;
            $credit = (float) $row->total_credit;

            if ($row->type === 'Asset') {
                // Normal Debit balance
                $balance  = $debit - $credit;
                $assets[] = [
                    'code'    => $row->code,
                    'name'    => $row->name,
                    'balance' => $balance,
                ];
                $totalAssets += $balance;

            } elseif ($row->type === 'Liability') {
                // Normal Credit balance
                $balance       = $credit - $debit;
                $liabilities[] = [
                    'code'    => $row->code,
                    'name'    => $row->name,
                    'balance' => $balance,
                ];
                $totalLiabilities += $balance;

            } elseif ($row->type === 'Net Asset') {
                // Normal Credit balance
                $balance     = $credit - $debit;
                $netAssets[] = [
                    'code'    => $row->code,
                    'name'    => $row->name,
                    'balance' => $balance,
                ];
                $totalNetAssets += $balance;
            }
        }

        // ── Inject virtual "Surplus / Defisit Berjalan" into Net Assets ──────
        $netAssets[] = [
            'code'       => 'AUTO',
            'name'       => $surplusDeficit >= 0
                                ? 'Surplus Berjalan (Pendapatan - Beban)'
                                : 'Defisit Berjalan (Pendapatan - Beban)',
            'balance'    => $surplusDeficit,
            'is_virtual' => true,
        ];
        $totalNetAssets += $surplusDeficit;

        $totalLiabilitiesNetAssets = $totalLiabilities + $totalNetAssets;

        return [
            'as_of_date'                  => $this->as_of_date,
            'assets'                      => $assets,
            'total_assets'                => $totalAssets,
            'liabilities'                 => $liabilities,
            'total_liabilities'           => $totalLiabilities,
            'net_assets'                  => $netAssets,
            'total_net_assets'            => $totalNetAssets,
            'total_liabilities_net_assets'=> $totalLiabilitiesNetAssets,
            'surplus_deficit'             => $surplusDeficit,
            'is_balanced'                 => abs($totalAssets - $totalLiabilitiesNetAssets) < 0.01,
        ];
    }

    // ── Helper: format date in Bahasa Indonesia ──────────────────────────────
    public function formatDateId(string $date): string
    {
        static $months = [
            1 => 'Januari',  2 => 'Februari', 3 => 'Maret',     4 => 'April',
            5 => 'Mei',      6 => 'Juni',      7 => 'Juli',      8 => 'Agustus',
            9 => 'September',10 => 'Oktober',  11 => 'November', 12 => 'Desember',
        ];
        $d = Carbon::parse($date);
        return $d->day . ' ' . $months[(int) $d->month] . ' ' . $d->year;
    }
}
