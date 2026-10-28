<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContributorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'contributor_type',
        'organization_name',
        'pic_name',
        'phone',
        'address',
        'identity_number',
        'identity_document_path',
        'website',
        'verification_status',
        'verified_by',
        'verified_at',
        'verification_notes',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function volunteerProfile(): HasOne
    {
        return $this->hasOne(VolunteerProfile::class);
    }

    public function pledges(): HasMany
    {
        return $this->hasMany(Pledge::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->contributor_type === 'organization' && !empty($this->organization_name)) {
            return $this->organization_name;
        }
        return $this->user ? $this->user->name : 'Donatur';
    }
}
