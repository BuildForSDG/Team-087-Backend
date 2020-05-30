<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    public $incrementing = false;
    protected $primaryKey = 'users_id';

    protected $fillable = [
        'users_id', 'card_no', 'blood_group', 'genotype', 'eye_colour', 'skin_colour'
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
