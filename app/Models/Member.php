<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'baptism_date' => 'date',
            'sidi_date' => 'date',
            'marriage_date' => 'date',
            'death_date' => 'date',
            'status_baptis' => 'boolean',
            'is_deceased' => 'boolean',
            'income' => 'decimal:2',
        ];
    }

    public function fullName(): Attribute
    {
        return Attribute::get(function (): string {
            return collect([$this->first_name, $this->middle_name, $this->last_name])
                ->filter()
                ->implode(' ');
        });
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    public function familyPosition(): BelongsTo
    {
        return $this->belongsTo(DataDictionary::class, 'family_position_id');
    }

    public function maritalStatus(): BelongsTo
    {
        return $this->belongsTo(DataDictionary::class, 'marital_status_id');
    }

    public function education(): BelongsTo
    {
        return $this->belongsTo(DataDictionary::class, 'education_id');
    }

    public function occupation(): BelongsTo
    {
        return $this->belongsTo(DataDictionary::class, 'occupation_id');
    }

    public function churchRole(): BelongsTo
    {
        return $this->belongsTo(DataDictionary::class, 'church_role_id');
    }

    public function membershipStatus(): BelongsTo
    {
        return $this->belongsTo(DataDictionary::class, 'membership_status_id');
    }

    public function mutations(): HasMany
    {
        return $this->hasMany(MemberMutation::class);
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(MemberContribution::class);
    }
}
