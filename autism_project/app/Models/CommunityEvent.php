<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommunityEvent extends Model
{
    use HasFactory;

    // Matches the exact columns from your migration
    protected $fillable = [
        'title',
        'category',
        'location',
        'event_date',
        'event_time',
        'description',
        'status'
    ];

    // Relationship: The specialists who have registered for this event
    public function specialists()
    {
        return $this->belongsToMany(
            Specialist::class,
            'community_event_specialist',
            'community_event_id',
            'specialist_id'
        )->withTimestamps();
    }
}
