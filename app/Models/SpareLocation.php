<?php

namespace App\Models;

class SpareLocation extends BaseModel
{
    protected $table = 'xcelr8_us_spare_location';
    protected $fillable = [
        'spare_branch_id',
        'name',
        'abbr',
        'demibranch',
        'stock_location',
        'd_order',
        'service_branch',
        'spare_consumption',
        'spare_store',
        'spare_warehouse',
        'status',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
    protected $translatable = ['name', 'abbr'];

    public function spareBranch()
    {
        return $this->belongsTo(SpareBranch::class, 'spare_branch_id');
    }

    public function users()
    {
        return $this->belongsToMany(\App\User::class, 'xcore_user_spare_locations', 'spare_location_id', 'user_id');
    }
}
