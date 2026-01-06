<?php

namespace App\Models;

class Color extends BaseModel
{
    protected $table = 'xcore_colors';
    protected $fillable = ['segment_id', 'subsegment_id', 'model_id', 'variant_id', 'name', 'code', 'hexcode', 'image', 'status', 'created_by', 'updated_by', 'deleted_by'];

    public function segment()
    {
        return $this->belongsTo(Segment::class, 'segment_id');
    }

    public function subsegment()
    {
        return $this->belongsTo(Subsegment::class, 'subsegment_id');
    }

    public function model()
    {
        return $this->belongsTo(Model::class, 'model_id');
    }

    public function variant()
    {
        return $this->belongsTo(Variant::class, 'variant_id');
    }

    // Scope for fetching colors by segment
    public function scopeBySegment($query, $segmentId)
    {
        return $query->where('segment_id', $segmentId);
    }
}
