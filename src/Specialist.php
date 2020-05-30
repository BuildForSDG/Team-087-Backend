<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Mental-Health Specialist Model (Psychiatrists, Psychologists, e.t.c)
 * 
 * @property string $license_no
 * @property \DateTime $license_at
 * @property \DateTime $last_renewed_at
 * @property \DateTime $expires_at
 */
class Specialist extends Model
{
    public $incrementing = false;
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_id', 'license_no', 'licensed_at', 'last_renewed_at', 'expires_at',
    ];

    protected $dates = [
        'licensed_at', 'last_renewed_at', 'expires_at'
    ];

    protected $hidden = ['user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'specialist_id');
    }
}
