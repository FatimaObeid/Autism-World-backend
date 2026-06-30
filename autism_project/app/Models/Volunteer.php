<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Volunteer extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $fillable = ['id', 'activity',  'phone'];
    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'id');
    }
    public function workshops()
    {
        return $this->hasMany(Workshop::class);
    }
}
