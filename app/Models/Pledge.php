<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pledge extends Model
{
    use HasFactory;

    protected $fillable = [
        'need_id',
        'contributor_profile_id',
        'tracking_code',
        'quantity',
        'notes',
        'estimated_delivery_at',
        'status',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
        'completed_at',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'estimated_delivery_at' => 'date',
        'reviewed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function need(): BelongsTo
    {
        return $this->belongsTo(Need::class);
    }

    public function contributorProfile(): BelongsTo
    {
        return $this->belongsTo(ContributorProfile::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function trackingLogs(): HasMany
    {
        return $this->hasMany(TrackingLog::class)->orderBy('created_at', 'asc');
    }

    public function evidenceFiles(): HasMany
    {
        return $this->hasMany(EvidenceFile::class)->latest();
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'submitted' => 'Diajukan',
            'verified' => 'Diverifikasi',
            'in_delivery' => 'Dalam Pengiriman',
            'received' => 'Diterima',
            'completed' => 'Selesai',
            'rejected' => 'Ditolak',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    public function getAllowedNextStatuses(): array
    {
        return match ($this->status) {
            'submitted' => ['verified', 'rejected', 'cancelled'],
            'verified' => ['in_delivery', 'cancelled'],
            'in_delivery' => ['received'],
            'received' => ['completed'],
            'completed' => [],
            'rejected' => [],
            'cancelled' => [],
            default => [],
        };
    }
}
