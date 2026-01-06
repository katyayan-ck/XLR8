<?php

namespace App\Models;

class UserLocation extends BaseModel
{
    protected $table = 'xcore_user_locations';
    protected $fillable = ['user_id', 'location_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
