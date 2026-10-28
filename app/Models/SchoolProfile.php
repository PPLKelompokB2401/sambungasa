<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'school_name',
        'npsn',
        'address',
        'province_id',
        'regency_id',
        'district',
        'postal_code',
        'phone',
        'principal_name',
        'student_count',
        'teacher_count',
        'latitude',
        'longitude',
        'verification_status',
        'verified_by',
        'verified_at',
        'verification_notes',
        'photo_path',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'verified_at' => 'datetime',
        'student_count' => 'integer',
        'teacher_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'province_id');
    }

    public function regency(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'regency_id');
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'regency_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function needs(): HasMany
    {
        return $this->hasMany(Need::class);
    }

    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }
}
