<?php

namespace App\Models;

class SubDepartment extends BaseModel
{
    protected $table = 'xcelr8_us_sub_department';
    protected $fillable = ['department_id', 'name', 'abbr', 'status', 'created_by', 'updated_by', 'deleted_by'];
    protected $translatable = ['name', 'abbr'];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'xcore_user_sub_departments', 'sub_department_id', 'user_id');
    }
}
