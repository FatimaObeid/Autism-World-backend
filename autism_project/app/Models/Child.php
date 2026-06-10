<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\ParentProfile;

class Child extends Model
{
    use HasFactory;

   protected $fillable = [
    'parent_profile_id',
    'specialist_id', // ADD THIS
    'full_name',
    'dob',
    'gender',
    'age',
    'autism_level',
    'description',
    'has_other_disease',
    'medical_condition',
    'diagnosis',
    'therapy_type',
    'session_frequency',
    'last_session',
    'next_plan',
    'current_goals',
    'recent_progress',
    'important_notes',
];
    public function parent()
    {
        return $this->belongsTo(ParentProfile::class, 'parent_profile_id');
    }
    public function specialist()
    {
        return $this->belongsTo(User::class, 'specialist_id');
    }


    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
