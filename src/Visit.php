<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $fillable = [
        'appointment_id', 'temperature', 'blood_pressure', 'height', 'visuals'
    ];

    protected $hidden = ['appointment_id'];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
