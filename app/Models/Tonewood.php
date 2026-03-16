<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tonewood extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'origin',
        'description',
        'characteristics',
        'image',
        'sort_order',
    ];

    protected $casts = [
        'characteristics' => 'array',
    ];

    // Auto-generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tonewood) {
            if (empty($tonewood->slug)) {
                $tonewood->slug = Str::slug($tonewood->name);
            }
        });

        static::updating(function ($tonewood) {
            if ($tonewood->isDirty('name') && !$tonewood->isDirty('slug')) {
                $tonewood->slug = Str::slug($tonewood->name);
            }
        });
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    // Get route key name for slug-based routing
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
