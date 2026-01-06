<?php

namespace App\Models;

class ServiceLocation extends BaseModel
{
    protected $table = 'xcelr8_us_service_location';
    protected $fillable = [
        'service_branch_id',
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

    public function serviceBranch()
    {
        return $this->belongsTo(ServiceBranch::class, 'service_branch_id');
    }

    public function users()
    {
        return $this->belongsToMany(\App\User::class, 'xcore_user_service_locations', 'service_location_id', 'user_id');
    }
}
