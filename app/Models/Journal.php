<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Journal extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'is_locked' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Journal $journal) {
            if (empty($journal->created_by)) {
                $journal->created_by = auth()->id() ?? (\App\Models\User::first()->id ?? 1);
            }

            if (empty($journal->transaction_number)) {
                $date = Carbon::parse($journal->transaction_date ?? now());
                $yearMonth = $date->format('Ym');

                $latest = static::whereYear('transaction_date', $date->year)
                    ->whereMonth('transaction_date', $date->month)
                    ->orderBy('transaction_number', 'desc')
                    ->first();

                if ($latest) {
                    $lastSequence = (int) substr($latest->transaction_number, -4);
                    $nextSequence = str_pad($lastSequence + 1, 4, '0', STR_PAD_LEFT);
                } else {
                    $nextSequence = '0001';
                }

                $journal->transaction_number = "JRN-{$yearMonth}-{$nextSequence}";
            }
        });
    }

    public function getTotalNominalAttribute(): float
    {
        return (float) ($this->items->sum('debit') ?? 0.0);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(JournalItem::class);
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(MemberContribution::class);
    }
}
