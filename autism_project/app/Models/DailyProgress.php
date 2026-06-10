<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyProgress extends Model
{
    use HasFactory;

    // Explicitly defining the table name to match database conventions
    protected $table = 'daily_progress';

    // Fields that are safe for mass-assignment via the API payload
    protected $fillable = [
        'child_id',
        'date',
        'mood_level',
        'sensory_play',
        'social_interaction',
        'notes',
    ];

    /**
     * Data Type Casting
     * Ensures boolean fields and integers return properly formatted to the Flutter client.
     */
    protected $casts = [
        'date'               => 'date',
        'mood_level'         => 'integer',
        'sensory_play'       => 'boolean',
        'social_interaction' => 'boolean',
    ];

    /**
     * Relationship: A progress log entry belongs to one specific Child profile.
     */
    public function child()
    {
        return $this->belongsTo(Child::class, 'child_id');
    }
}