<?php

namespace App\Models;

class VehicleModel extends BaseModel
{
    protected $table = 'xcelr8_us_model';
    protected $fillable = ['segment_id', 'subsegment_id', 'oem_name', 'name', 'status', 'created_by', 'updated_by', 'deleted_by'];
    protected $translatable = ['oem_name', 'name'];

    public function segment()
    {
        return $this->belongsTo(Segment::class, 'segment_id');
    }

    public function subsegment()
    {
        return $this->belongsTo(Subsegment::class, 'subsegment_id');
    }

    public function variants()
    {
        return $this->hasMany(Variant::class, 'model_id');
    }

    public function colors()
    {
        return $this->hasMany(Color::class, 'model_id');
    }
}
