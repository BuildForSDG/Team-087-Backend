<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'specialist_id', 'patient_id', 'purpose', 'starts_at', 'ends_at', 'status', 'reason'
    ];

    protected $hidden = ['specialist_id', 'patient_id'];

    protected $dates = ['starts_at', 'ends_at'];

    protected $with = ['chats'];

    public function specialist()
    {
        return $this->belongsTo(Specialist::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }
}
