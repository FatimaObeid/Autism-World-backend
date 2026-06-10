<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ParentProfile extends Model
{
    use HasFactory;

    protected $table = 'parent_profiles';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'dob',
        'phone',
        'address',
        'gender',
        'therapy_type',
        'status',
        'notes',
    ];



    // Parent belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class, 'id','id');
    }

    // Parent has one child
    public function child()
    {
        return $this->hasOne(Child::class, 'parent_profile_id');
    }

    // Parent appointments
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'parent_profile_id');
    }

    // Specialists through appointments

    public function specialists()
    {
        return $this->belongsToMany(
            Specialist::class,
            'appointments',
            'parent_profile_id',
            'specialist_id'
        )->withPivot([
            'appointment_date',
            'appointment_time',
            'therapy_type',
            'status',
            'notes',
        ])->withTimestamps();
    }

        public function communityEvents()
    {
        return $this->belongsToMany(
            CommunityEvent::class,
            'community_event_parent',
            'parent_profile_id',
            'community_event_id'
        )->withTimestamps();
    }
}