<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Rayon extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function families(): HasMany
    {
        return $this->hasMany(Family::class);
    }

    // Tambahkan relasi penjembatan ini
    public function members(): HasManyThrough
    {
        /* * Argumen 1: Model tujuan akhir (Member)
         * Argumen 2: Model perantara (Family)
         */
        return $this->hasManyThrough(Member::class, Family::class);
    }
}