<?php
// app/Models/Department.php
namespace App\Models;

class Department extends BaseModel
{
    protected $table = 'xcelr8_us_department';
    protected $fillable = ['name', 'abbr', 'status', 'created_by', 'updated_by', 'deleted_by'];
    protected $translatable = ['name', 'abbr'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'xcore_user_departments', 'department_id', 'user_id');
    }
}
