<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Care-Group Members/Users Model
 */
class GroupMember extends Pivot
{
    protected $fillable = [
        'group_id', 'user_id', 'blocked'
    ];

    protected $casts = [
        'blocked' => 'boolean'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
