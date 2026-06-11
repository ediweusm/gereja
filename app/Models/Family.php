<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Family extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function scopeNeedsAssistance(Builder $query): Builder
    {
        return $query->whereHas('houseCategory', function ($q) {
            $q->whereIn('code', ['darurat', 'semi-permanen']);
        });
    }

    public function rayon(): BelongsTo
    {
        return $this->belongsTo(Rayon::class);
    }

    public function houseCategory(): BelongsTo
    {
        return $this->belongsTo(DataDictionary::class, 'house_category_id');
    }

    public function houseStatus(): BelongsTo
    {
        return $this->belongsTo(DataDictionary::class, 'house_status_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }
}
