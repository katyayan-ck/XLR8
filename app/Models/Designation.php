<?php
// app/Models/Designation.php
namespace App\Models;

class Designation extends BaseModel
{
    protected $table = 'xcelr8_us_designation';
    protected $fillable = ['name', 'abbr', 'status', 'created_by', 'updated_by', 'deleted_by'];
    protected $translatable = ['name', 'abbr'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'xcore_user_designations', 'designation_id', 'user_id');
    }
}
