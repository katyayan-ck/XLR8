<?php

namespace App\Models;

class UserSegment extends BaseModel
{
    protected $table = 'xcore_user_segments';
    protected $fillable = ['user_id', 'segment_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function segment()
    {
        return $this->belongsTo(Segment::class, 'segment_id');
    }
}
