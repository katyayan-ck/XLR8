<?php

namespace App\Models;

class ServiceBranch extends BaseModel
{
    protected $table = 'xcelr8_us_service_branch';
    protected $fillable = ['name', 'abbr', 'status', 'created_by', 'updated_by', 'deleted_by'];
    protected $translatable = ['name', 'abbr'];

    public function serviceLocations()
    {
        return $this->hasMany(ServiceLocation::class, 'service_branch_id');
    }

    public function users()
    {
        return $this->belongsToMany(\App\User::class, 'xcore_user_service_branches', 'service_branch_id', 'user_id');
    }
}
