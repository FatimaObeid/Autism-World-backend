<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


use App\Models\ParentProfile;
use App\Models\Specialist;

class Appointment extends Model
{
    use HasFactory;
    protected $fillable = ['parent_id', 'specialist_id', 'appointment_id', 'status', 'appointment_time'];

    public function parentprofile()
    {
        return $this->belongsTo(ParentProfile::class, 'parent_id');
    }
    public function specialist()
    {
        return $this->belongsTo(Specialist::class, 'specialist_id');
    }
}
