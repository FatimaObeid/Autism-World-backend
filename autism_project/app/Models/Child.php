<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\ParentProfile;

class Child extends Model
{
    use HasFactory;
    protected $fillable=[];
    public function parent(){
        return $this->belongsTo(ParentProfile::class,'parent_id');
    }
}
