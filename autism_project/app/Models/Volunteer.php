<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Volunteer extends Model
{

    protected $fillable = ['activity', 'name', 'location', 'phone'];
    public function workshops()
    {
        return $this->hasMany(Workshop::class);
    }
}
