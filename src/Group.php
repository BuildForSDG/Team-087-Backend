<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Care-Groups Model
 * 
 * @property string $name The name of the group
 * @property string $purpose The purpose of creating the group
 */
class Group extends Model
{
    protected $fillable = [
        'name', 'purpose', 'user_id'
    ];

    /**
     * Group Creator / Admin
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class)->using(GroupMember::class)->as('participant')->withPivot(['blocked'])->withTimestamps();
    }
}
