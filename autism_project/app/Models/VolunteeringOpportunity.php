<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VolunteeringOpportunity extends Model
{

    protected $fillable = ['activity', 'name', 'location', 'phone'];
}
