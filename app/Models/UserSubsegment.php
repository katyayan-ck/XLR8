<?php

namespace App\Models;

class UserSubsegment extends BaseModel
{
    protected $table = 'xcore_user_subsegments';
    protected $fillable = ['user_id', 'subsegment_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function subsegment()
    {
        return $this->belongsTo(Subsegment::class, 'subsegment_id');
    }
}
