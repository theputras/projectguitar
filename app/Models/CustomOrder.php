<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CustomOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'customer_email',
        'customer_country',
        'customer_phone',
        'order_type',
        'requirements',
        'budget',
        'timeline',
        'current_step',
        'notes',
        'admin_notes',
        'tracking_token',
        'completed_at',
    ];

    protected $casts = [
        'requirements' => 'array',
        'completed_at' => 'datetime',
    ];

    // Auto-generate tracking token on create
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->tracking_token)) {
                $order->tracking_token = Str::uuid()->toString();
            }
        });
    }

    // Steps definition
    public const STEPS = [
        'consultation' => ['label' => 'Consultation', 'order' => 1],
        'design'       => ['label' => 'Design', 'order' => 2],
        'build'        => ['label' => 'Build', 'order' => 3],
        'quality_check' => ['label' => 'Quality Check', 'order' => 4],
        'shipping'     => ['label' => 'Shipping', 'order' => 5],
        'completed'    => ['label' => 'Completed', 'order' => 6],
    ];

    // Helper: get current step order number
    public function getCurrentStepOrder()
    {
        return self::STEPS[$this->current_step]['order'] ?? 0;
    }

    // Helper: check if a step is completed
    public function isStepCompleted($step)
    {
        $stepOrder = self::STEPS[$step]['order'] ?? 0;
        return $this->getCurrentStepOrder() > $stepOrder;
    }

    // Scopes
    public function scopeByStep($query, $step)
    {
        return $query->where('current_step', $step);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('current_step', ['completed']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('current_step', 'completed');
    }
}
