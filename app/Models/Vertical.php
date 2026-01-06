<?php
// app/Models/Vertical.php
namespace App\Models;

class Vertical extends BaseModel
{
    protected $table = 'xcelr8_us_vertical';
    protected $fillable = ['name', 'status', 'created_by', 'updated_by', 'deleted_by'];
    protected $translatable = ['name'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'xcore_user_verticals', 'vertical_id', 'user_id');
    }
}
