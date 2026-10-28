<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NeedCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'assistance_type',
        'description',
        'icon',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function needs(): HasMany
    {
        return $this->hasMany(Need::class);
    }
}
