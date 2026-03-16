<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Portfolio extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'description',
        'image',
        'wood_type',
        'pickup',
        'scale_length',
        'finish',
        'strings',
        'price_range',
        'gallery',
        'specifications',
        'is_featured',
        'status',
    ];

    protected $casts = [
        'gallery' => 'array',
        'specifications' => 'array',
        'is_featured' => 'boolean',
    ];

    // Auto-generate slug on create
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($portfolio) {
            if (empty($portfolio->slug)) {
                $portfolio->slug = Str::slug($portfolio->title);
            }
        });

        static::updating(function ($portfolio) {
            if ($portfolio->isDirty('title') && !$portfolio->isDirty('slug')) {
                $portfolio->slug = Str::slug($portfolio->title);
            }
        });
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeBass($query)
    {
        return $query->where('category', 'bass');
    }

    public function scopeGuitar($query)
    {
        return $query->where('category', 'guitar');
    }

    // Get route key name for slug-based routing
    public function getRouteKeyName()
    {
        return 'slug';
    }
}