<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountMonthlyBalance extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'beginning_balance' => 'decimal:2',
            'debit_mutation' => 'decimal:2',
            'credit_mutation' => 'decimal:2',
            'ending_balance' => 'decimal:2',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
