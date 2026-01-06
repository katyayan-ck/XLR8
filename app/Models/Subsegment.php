<?php

namespace App\Models;

class Subsegment extends BaseModel
{
    protected $table = 'xcore_subsegments';
    protected $fillable = ['segment_id', 'name', 'type', 'status', 'created_by', 'updated_by', 'deleted_by'];
    protected $translatable = ['name'];

    public function segment()
    {
        return $this->belongsTo(Segment::class, 'segment_id');
    }

    public function models()
    {
        return $this->hasMany(VehicleModel::class, 'subsegment_id');
    }

    public function variants()
    {
        return $this->hasMany(Variant::class, 'subsegment_id');
    }

    public function colors()
    {
        return $this->hasMany(Color::class, 'subsegment_id');
    }
}
