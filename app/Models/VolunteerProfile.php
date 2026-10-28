<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VolunteerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'contributor_profile_id',
        'education',
        'expertise',
        'experience_years',
        'availability',
        'bio',
    ];

    protected $casts = [
        'experience_years' => 'integer',
    ];

    public function contributorProfile(): BelongsTo
    {
        return $this->belongsTo(ContributorProfile::class);
    }
}
