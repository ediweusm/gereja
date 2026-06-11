<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventAssignment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function ministryRole(): BelongsTo
    {
        return $this->belongsTo(MinistryRole::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
