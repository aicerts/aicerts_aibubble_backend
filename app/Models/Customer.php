<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
    use HasFactory;
    use HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'status',
        'password',
        'profile_image'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['profile_image'];

    /**
     * Get the user's profile image URL.
     *
     * @return string|null
     */
    public function getProfileImageAttribute()
    {
        if ($this->attributes['profile_image']) {
            return asset($this->attributes['profile_image']);
        } else {
            $this->attributes['profile_image'];
        }
    }
}
