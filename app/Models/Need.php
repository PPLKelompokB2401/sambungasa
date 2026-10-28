<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Need extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_profile_id',
        'need_category_id',
        'title',
        'description',
        'assistance_type',
        'priority',
        'quantity_required',
        'quantity_fulfilled',
        'unit',
        'status',
        'rejection_notes',
        'reviewed_by',
        'reviewed_at',
        'published_at',
        'target_date',
    ];

    protected $casts = [
        'quantity_required' => 'integer',
        'quantity_fulfilled' => 'integer',
        'reviewed_at' => 'datetime',
        'published_at' => 'datetime',
        'target_date' => 'date',
    ];

    public function schoolProfile(): BelongsTo
    {
        return $this->belongsTo(SchoolProfile::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(NeedCategory::class, 'need_category_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function pledges(): HasMany
    {
        return $this->hasMany(Pledge::class);
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isOpenForPledges(): bool
    {
        if ($this->status !== 'published') {
            return false;
        }

        if ($this->quantity_required !== null && $this->quantity_required > 0) {
            return $this->getRemainingQuantity() > 0;
        }

        return true;
    }

    public function getActivePledgesQuantity(): int
    {
        return (int) $this->pledges()
            ->whereIn('status', ['submitted', 'verified', 'in_delivery', 'received', 'completed'])
            ->sum('quantity');
    }

    public function getRemainingQuantity(): int
    {
        if ($this->quantity_required === null) {
            return 0;
        }
        $committed = $this->getActivePledgesQuantity();
        return max(0, $this->quantity_required - $committed);
    }

    public function getProgressPercentageAttribute(): int
    {
        if ($this->quantity_required === null || $this->quantity_required <= 0) {
            return $this->status === 'fulfilled' ? 100 : 0;
        }

        $percentage = ($this->quantity_fulfilled / $this->quantity_required) * 100;
        return (int) min(100, round($percentage));
    }
}
