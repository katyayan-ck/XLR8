<?php

namespace App\Models;

class Variant extends BaseModel
{
    protected $table = 'xcore_variants';
    protected $fillable = [
        'name',
        'model_id',
        'segment_id',
        'subsegment_id',
        'model_code',
        'model_name',
        'variant_name',
        'custom_model',
        'cvariant',
        'disp_name',
        'seg',
        'sub_seg',
        'clr_code',
        'color',
        'fuel',
        'gst',
        'seating',
        'wheels',
        'transmission',
        'drivetrain',
        'bodymake',
        'bodytype',
        'cc',
        'cc_range',
        'gvw',
        'permit',
        'status',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
    protected $translatable = ['name', 'model_name', 'variant_name', 'custom_model', 'cvariant', 'disp_name', 'color'];

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
        return $this->belongsTo(VehicleModel::class, 'model_id');
    }

    public function colors()
    {
        return $this->hasMany(Color::class, 'variant_id');
    }

    public function scopeBySegment($query, $segmentId)
    {
        return $query->where('segment_id', $segmentId);
    }
}
