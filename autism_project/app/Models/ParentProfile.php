<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class ParentProfile extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'dob', 'phone', 'address', 'gender'];
    public $incrementing = false;
    protected $primaryKey = 'id';


    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'parent_profile_id');
    }
    public function child()
    {
        return $this->hasOne(Child::class, 'parent_id');
    }
    public function specialists()
    {
        return $this->belongsToMany(Specialist::class, 'appointments', 'parent_profile_id', 'specialist_id')
            ->withPivot('appointment_time', 'status')
            ->withTimestamps();
    }
}
