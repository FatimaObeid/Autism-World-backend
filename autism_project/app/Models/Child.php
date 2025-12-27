<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\ParentProfile;

class Child extends Model
{
    use HasFactory;
    protected $fillable=['parentprofile_id','firstname','lastname','dob','gender','autismtype'];
    public function parent(){
        return $this->belongsTo(ParentProfile::class,'parentprofile_id');
    }
}
