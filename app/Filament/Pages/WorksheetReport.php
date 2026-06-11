<?php

namespace App\Filament\Pages;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class WorksheetReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $navigationGroup = 'Manajemen Keuangan';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationLabel = 'Neraca Lajur';
    protected static ?string $title = 'Neraca Lajur (Worksheet)';

    protected static string $view = 'filament.pages.worksheet-report';

    public ?string $start_date = null;
    public ?string $end_date = null;

    public function mount(): void
    {
        $this->start_date = now()->startOfYear()->format('Y-m-d');
        $this->end_date = now()->endOfYear()->format('Y-m-d');

        $this->form->fill([
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                    ->schema([
                        DatePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->required()
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->live(),
                        DatePicker::make('end_date')
                            ->label('Tanggal Akhir')
                            ->required()
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->live(),
                    ]),
            ]);
    }

    public function getReportDataProperty(): array
    {
        if (blank($this->start_date) || blank($this->end_date)) {
            return [];
        }

        $startDate = Carbon::parse($this->start_date)->startOfDay();
        $endDate = Carbon::parse($this->end_date)->endOfDay();

        // Query active accounts and sum debit & credit in date range
        $queryResults = DB::table('accounts')
            ->leftJoin('journal_items', 'accounts.id', '=', 'journal_items.account_id')
            ->leftJoin('journals', function ($join) use ($startDate, $endDate) {
                $join->on('journals.id', '=', 'journal_items.journal_id')
                    ->whereBetween('journals.transaction_date', [$startDate, $endDate]);
            })
            ->where('accounts.is_active', true)
            ->select([
                'accounts.id',
                'accounts.code',
                'accounts.name',
                'accounts.type',
                DB::raw('SUM(CASE WHEN journals.id IS NOT NULL THEN journal_items.debit ELSE 0 END) as total_debit'),
                DB::raw('SUM(CASE WHEN journals.id IS NOT NULL THEN journal_items.credit ELSE 0 END) as total_credit'),
            ])
            ->groupBy('accounts.id', 'accounts.code', 'accounts.name', 'accounts.type')
            ->orderBy('accounts.code')
            ->get();

        $rows = [];
        $tb_debit = 0.0;
        $tb_credit = 0.0;
        $pl_debit = 0.0;
        $pl_credit = 0.0;
        $bs_debit = 0.0;
        $bs_credit = 0.0;

        foreach ($queryResults as $row) {
            $debit = (float) $row->total_debit;
            $credit = (float) $row->total_credit;

            if ($debit == 0 && $credit == 0) {
                continue;
            }

            $row_tb_debit = 0.0;
            $row_tb_credit = 0.0;
            $row_pl_debit = 0.0;
            $row_pl_credit = 0.0;
            $row_bs_debit = 0.0;
            $row_bs_credit = 0.0;

            if ($debit > $credit) {
                $balance = $debit - $credit;
                $row_tb_debit = $balance;
                $tb_debit += $balance;

                if (in_array($row->type, ['Revenue', 'Expense'])) {
                    $row_pl_debit = $balance;
                    $pl_debit += $balance;
                } else {
                    $row_bs_debit = $balance;
                    $bs_debit += $balance;
                }
            } elseif ($credit > $debit) {
                $balance = $credit - $debit;
                $row_tb_credit = $balance;
                $tb_credit += $balance;

                if (in_array($row->type, ['Revenue', 'Expense'])) {
                    $row_pl_credit = $balance;
                    $pl_credit += $balance;
                } else {
                    $row_bs_credit = $balance;
                    $bs_credit += $balance;
                }
            } else {
                continue;
            }

            $rows[] = [
                'code' => $row->code,
                'name' => $row->name,
                'type' => $row->type,
                'tb_debit' => $row_tb_debit,
                'tb_credit' => $row_tb_credit,
                'pl_debit' => $row_pl_debit,
                'pl_credit' => $row_pl_credit,
                'bs_debit' => $row_bs_debit,
                'bs_credit' => $row_bs_credit,
            ];
        }

        // Calculate Surplus / Defisit (Revenue Credit - Expense Debit)
        $surplus_deficit = $pl_credit - $pl_debit;

        return [
            'rows' => $rows,
            'totals' => [
                'tb_debit' => $tb_debit,
                'tb_credit' => $tb_credit,
                'pl_debit' => $pl_debit,
                'pl_credit' => $pl_credit,
                'bs_debit' => $bs_debit,
                'bs_credit' => $bs_credit,
            ],
            'surplus_deficit' => $surplus_deficit,
        ];
    }
}
