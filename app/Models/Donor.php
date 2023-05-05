<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Donor extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    /** 
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'rhFactor',
        'bloodGroup',
        'address',
        'phone',
        'gender',
        'dob',
        'weight',
        "active"
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }
    function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
