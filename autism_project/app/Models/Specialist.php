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

    protected $fillable = ['id', 'specialization', 'license', 'years_of_experience', 'bio', 'location', 'status'];
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
        return $this->belongsToMany(ParentProfile::class, 'appointments', 'specialist_id', 'parent_profile_id')
            ->withPivot('appointment_time', 'status')->withTimestamps();
    }

    public function workshops()
    {
        return $this->belongsToMany(
            Workshop::class,
            'specialist_workshop',
            'specialist_id',
            'workshop_id'
        )->withTimestamps();
    }
    public function children()
    {

        return $this->hasMany(Child::class, 'specialist_id');
    }
}
