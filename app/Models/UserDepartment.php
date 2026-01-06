<?php

namespace App\Models;

class UserDepartment extends BaseModel
{
    protected $table = 'xcore_user_departments';
    protected $fillable = ['user_id', 'department_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
