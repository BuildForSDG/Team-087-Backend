<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    public $timestamps = true;
    public $incrementing = false;
    protected $primaryKey = 'users_id';
}
