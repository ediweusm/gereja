<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
        ];
    }

    public function rayon(): BelongsTo
    {
        return $this->belongsTo(Rayon::class);
    }

    public function hostFamily(): BelongsTo
    {
        return $this->belongsTo(Family::class, 'host_family_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(EventAssignment::class);
    }
}
