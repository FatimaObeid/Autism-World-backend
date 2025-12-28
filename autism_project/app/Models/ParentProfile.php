<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class ParentProfile extends Model
{
    use HasFactory;

    protected $fillable=['id','dob','phonenumber','address'];
    public $incrementing=false;
    protected $primaryKey='id';


    public function user(){
        return $this->belongsTo(User::class,'id');
    }

    public function appointments(){
        return $this->hasMany(Appointment::class,'parentprofile_id');
    }

    public function child(){
        return $this->hasOne(Child::class,'parentprofile_id');
    }


}
