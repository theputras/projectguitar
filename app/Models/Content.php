<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Content extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'section',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    // Auto-generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($content) {
            if (empty($content->slug)) {
                $content->slug = Str::slug($content->title);
            }
        });
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeSection($query, $section)
    {
        return $query->where('section', $section);
    }

    // Helper to get content by section
    public static function getBySection($section)
    {
        return static::published()->section($section)->get();
    }

    // Helper to get single content by slug
    public static function getBySlug($slug)
    {
        return static::published()->where('slug', $slug)->first();
    }
}
