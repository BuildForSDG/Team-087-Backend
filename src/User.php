<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'last_name', 'first_name', 'gender', 'birth_date', 'email', 'phone_number', 'marital_status',
        'is_patient', 'is_specialist', 'is_guest', 'profile_code'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'verified_at', 'verify_code', 'is_specialist', 'is_guest',// 'is_admin', 'is_active',
        'remember_token', 'profile_code'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_patient' => 'boolean',
        'is_specialist' => 'boolean',
        'is_admin' => 'boolean',
        'is_active' => 'boolean',
        'is_guest' => 'boolean',
    ];

    public function patient()
    {
        return $this->hasOne(Patient::class);
    }

    public function specialist()
    {
        return $this->hasOne(Specialist::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class)->using(GroupMember::class)->as('member')->withTimestamps();
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    protected function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::needsRehash($password) ? Hash::make($password) : $password;
    }
}
