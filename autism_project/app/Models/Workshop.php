<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workshop extends Model
{
    protected $fillable = [
        'title',
        'age_group',
        'location',
        'status',
        'volunteer_id',
        'workshop_time',
        'date',
        'target_audience',
        'status',
    ];
    protected $casts = [
        'date' => 'date',
        'workshop_time' => 'datetime:H:i',
    ];
    public function volunteer()
    {
        return $this->belongsTo(Volunteer::class);
    }

    public function specialists()
    {
        return $this->belongsToMany(
            Specialist::class,
            'specialist_workshop',
            'workshop_id',
            'specialist_id'
        )->withTimestamps();
    }

    public function isSpecialistRegistered($specialistId)
    {
        return $this->specialists()->where('specialist_id', $specialistId)->exists();
    }
}
