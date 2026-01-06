<?php

namespace App\Models;

class UserSubbranch extends BaseModel
{
    protected $table = 'xcore_user_subbranches';
    protected $fillable = ['user_id', 'subbranch_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function subbranch()
    {
        return $this->belongsTo(Subbranch::class, 'subbranch_id');
    }
}
