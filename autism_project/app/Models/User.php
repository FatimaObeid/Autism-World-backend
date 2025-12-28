<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Models\Specialist;
use App\Models\ParentProfile;
use App\Models\Admin;

/**
 * @method bool isAdmin()
 * @method bool isParent()
 * @method bool isSpecialist()
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function specialist()
    {
        return $this->hasOne(Specialist::class, 'id');
    }

    public function parent()
    {
        return $this->hasOne(ParentProfile::class, 'id');
    }

    public function admin()
    {
        return $this->hasOne(Admin::class, 'id');
    }

    public function isSpecialist()
    {
        return $this->role === 'specialist';
    }

    public function isParent()
    {
        return $this->role === 'parent';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }





    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
