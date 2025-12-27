<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\User;

class Admin extends Model
{
    use HasFactory;
    
    protected $fillable=['id','department'];
    public $incrementing=false;
    protected $primaryKey='id';
    public function user(){
        return $this->belongsTo(User::class,'id');
    }
}
