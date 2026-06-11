<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberContribution extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function contributionType(): BelongsTo
    {
        return $this->belongsTo(DataDictionary::class, 'contribution_type_id');
    }
}
