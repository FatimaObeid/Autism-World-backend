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
        'workshop_time'
    ];

    public function volunteer()
    {
        return $this->belongsTo(Volunteer::class);
    }
}
