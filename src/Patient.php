<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Patient Model
 * 
 * @property string $card_no
 * @property string $blood_group
 * @property string $genotype
 * @property string $eye_colour
 * @property string $skin_colour
 */
class Patient extends Model
{
    public $incrementing = false;
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_id', 'card_no', 'blood_group', 'genotype', 'eye_colour', 'skin_colour'
    ];

    protected $hidden = ['user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    public function visits()
    {
        return $this->hasManyThrough(Visit::class, Appointment::class, 'patient_id', 'appointment_id', 'user_id');
    }
}
