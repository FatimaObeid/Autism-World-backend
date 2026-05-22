<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\User;
use App\Models\Appointment;
use App\Models\ParentProfile;

class Specialist extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'specialization', 'license'];
    public $incrementing = false;
    protected $primaryKey = 'id';

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'specialist_id');
    }

    public function parents()
    {
        return $this->belongsToMany(ParentProfile::class, 'appointments', 'specialist_id', 'parent_id')
            ->withPivot('appointment_time', 'status')->withTimestamps();
    }

    public function communityEvents()
    {
        return $this->belongsToMany(
            CommunityEvent::class,
            'community_event_specialist',
            'specialist_id',
            'community_event_id'
        )->withTimestamps();
    }
    public function children()
    {

        return $this->hasMany(Child::class, 'specialist_id');
    }
}
