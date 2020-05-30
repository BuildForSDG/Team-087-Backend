<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Specialist extends Model
{
    public $incrementing = false;
    protected $primaryKey = 'users_id';

    protected $fillable = [
        'users_id', 'license_no', 'licensed_at', 'last_renewed_at', 'expires_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
