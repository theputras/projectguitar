<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'country',
        'instrument',
        'instrument_type',
        'budget_range',
        'message',
        'inquiry_type',
        'status',
        'admin_notes',
    ];

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('inquiry_type', $type);
    }

    // Mark as read
    public function markAsRead()
    {
        $this->update(['status' => 'read']);
    }

    // Mark as responded
    public function markAsResponded()
    {
        $this->update(['status' => 'responded']);
    }
}
