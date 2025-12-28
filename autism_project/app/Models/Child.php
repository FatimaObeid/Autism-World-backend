<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\ParentProfile;

class Child extends Model
{
    use HasFactory;
    protected $fillable=['parent_profile_id','first_name','last_name','dob','gender','autism_type'];
    public function parent(){
        return $this->belongsTo(ParentProfile::class,'parent_profile_id');
    }
}
