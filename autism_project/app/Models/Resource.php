<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;
    protected $fillable = ['title_en', 'title_ar', 'category_en', 'category_ar', 'description_en', 'description_ar', 'icon'];
}
