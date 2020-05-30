<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'specialist_id', 'patient_id', 'remark', 'rating'
    ];

    protected $hidden = ['specialist_id', 'patient_id'];

    public function specialist()
    {
        return $this->belongsTo(Specialist::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
