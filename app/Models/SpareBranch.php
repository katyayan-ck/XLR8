<?php

namespace App\Models;

class SpareBranch extends BaseModel
{
    protected $table = 'xcelr8_us_spare_branch';
    protected $fillable = ['name', 'abbr', 'status', 'created_by', 'updated_by', 'deleted_by'];
    protected $translatable = ['name', 'abbr'];

    public function spareLocations()
    {
        return $this->hasMany(SpareLocation::class, 'spare_branch_id');
    }

    public function users()
    {
        return $this->belongsToMany(\App\User::class, 'xcore_user_spare_branches', 'spare_branch_id', 'user_id');
    }
}
