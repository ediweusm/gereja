<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberMutation extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'mutation_date' => 'date',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function oldRayon(): BelongsTo
    {
        return $this->belongsTo(Rayon::class, 'old_rayon_id');
    }

    public function newRayon(): BelongsTo
    {
        return $this->belongsTo(Rayon::class, 'new_rayon_id');
    }
}
