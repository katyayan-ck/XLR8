<?php

namespace App\Models;


class Segment extends BaseModel
{
    protected $table = 'xcelr8_us_segment';
    protected $fillable = ['name', 'type', 'status', 'created_by', 'updated_by', 'deleted_by'];
    protected $translatable = ['name'];

    public function subsegments()
    {
        return $this->hasMany(Subsegment::class, 'segment_id');
    }

    public function models()
    {
        return $this->hasMany(VehicleModel::class, 'segment_id');
    }

    public function variants()
    {
        return $this->hasMany(Variant::class, 'segment_id');
    }

    public function colors()
    {
        return $this->hasMany(Color::class, 'segment_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'xcore_user_segments', 'segment_id', 'user_id');
    }
}
