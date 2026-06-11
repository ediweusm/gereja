<?php

namespace App\Services;

use App\Models\Account;
use Illuminate\Support\Facades\DB;

class FinancialReportService
{
    /**
     * Generate Balance Sheet (Neraca Posisi Keuangan) as of a given date.
     *
     * Returns structured array:
     * [
     *   'as_of_date'            => string,
     *   'assets'                => [ ['code', 'name', 'balance'], ... ],
     *   'liabilities'           => [ ... ],
     *   'net_assets'            => [ ... ],  // includes virtual Surplus/Deficit row
     *   'total_assets'          => float,
     *   'total_liabilities'     => float,
     *   'total_net_assets'      => float,
     *   'total_liability_net'   => float,    // liabilities + net_assets
     *   'surplus_deficit'       => float,
     *   'is_balanced'           => bool,
     * ]
     */
    public function getBalanceSheet(string $asOfDate): array
    {
        // Single efficient query: SUM debit & credit per account
        // filtered by journal transaction_date <= $asOfDate
        $balances = DB::table('accounts')
            ->leftJoin('journal_items', 'journal_items.account_id', '=', 'accounts.id')
            ->leftJoin('journals', function ($join) use ($asOfDate) {
                $join->on('journals.id', '=', 'journal_items.journal_id')
                     ->where('journals.transaction_date', '<=', $asOfDate);
            })
            ->where('accounts.is_active', true)
            ->groupBy('accounts.id', 'accounts.code', 'accounts.name', 'accounts.type', 'accounts.parent_id')
            ->select([
                'accounts.id',
                'accounts.code',
                'accounts.name',
                'accounts.type',
                'accounts.parent_id',
                DB::raw('COALESCE(SUM(journal_items.debit), 0) as total_debit'),
                DB::raw('COALESCE(SUM(journal_items.credit), 0) as total_credit'),
            ])
            ->orderBy('accounts.code')
            ->get();

        $assets      = [];
        $liabilities = [];
        $netAssets   = [];
        $revenues    = [];
        $expenses    = [];

        foreach ($balances as $row) {
            $debit  = (float) $row->total_debit;
            $credit = (float) $row->total_credit;

            $balance = match ($row->type) {
                // Normal Debit: positive balance = debit > credit
                'Asset'     => $debit - $credit,
                'Expense'   => $debit - $credit,
                // Normal Credit: positive balance = credit > debit
                'Liability' => $credit - $debit,
                'Net Asset' => $credit - $debit,
                'Revenue'   => $credit - $debit,
                default     => 0.0,
            };

            $entry = [
                'id'        => $row->id,
                'code'      => $row->code,
                'name'      => $row->name,
                'parent_id' => $row->parent_id,
                'balance'   => $balance,
            ];

            match ($row->type) {
                'Asset'     => $assets[]      = $entry,
                'Liability' => $liabilities[] = $entry,
                'Net Asset' => $netAssets[]   = $entry,
                'Revenue'   => $revenues[]    = $entry,
                'Expense'   => $expenses[]    = $entry,
                default     => null,
            };
        }

        // Calculate Surplus/Deficit berjalan
        $totalRevenue  = collect($revenues)->sum('balance');
        $totalExpense  = collect($expenses)->sum('balance');
        $surplusDeficit = $totalRevenue - $totalExpense;

        // Only show accounts that are leaf nodes (no children) with non-zero balance,
        // OR all accounts if all are zero (empty DB). Filter parent headers out.
        $leafAccountIds = DB::table('accounts')
            ->whereNotIn('id', DB::table('accounts')->whereNotNull('parent_id')->pluck('parent_id'))
            ->pluck('id')
            ->toArray();

        $filterLeaf = fn ($items) => array_values(
            array_filter($items, fn ($item) => in_array($item['id'], $leafAccountIds))
        );

        $assets      = $filterLeaf($assets);
        $liabilities = $filterLeaf($liabilities);
        $netAssets   = $filterLeaf($netAssets);

        // Append virtual Surplus/Deficit row to Net Assets group
        $netAssets[] = [
            'id'        => null,
            'code'      => 'SURPLUS',
            'name'      => ($surplusDeficit >= 0 ? 'Surplus' : 'Defisit') . ' Berjalan (Otomatis)',
            'parent_id' => null,
            'balance'   => $surplusDeficit,
            'is_virtual' => true,
        ];

        $totalAssets     = array_sum(array_column($assets, 'balance'));
        $totalLiabilities = array_sum(array_column($liabilities, 'balance'));
        $totalNetAssets  = array_sum(array_column($netAssets, 'balance')); // includes surplus
        $totalLiabilityNet = $totalLiabilities + $totalNetAssets;

        return [
            'as_of_date'          => $asOfDate,
            'assets'              => $assets,
            'liabilities'         => $liabilities,
            'net_assets'          => $netAssets,
            'total_assets'        => $totalAssets,
            'total_liabilities'   => $totalLiabilities,
            'total_net_assets'    => $totalNetAssets,
            'total_liability_net' => $totalLiabilityNet,
            'surplus_deficit'     => $surplusDeficit,
            'is_balanced'         => abs($totalAssets - $totalLiabilityNet) < 0.01,
        ];
    }
}
