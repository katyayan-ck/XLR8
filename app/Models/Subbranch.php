<?php
// app/Models/Subbranch.php
namespace App\Models;

class Subbranch extends BaseModel
{
    protected $table = 'xcore_subbranches';
    protected $fillable = ['name', 'branch_id', 'created_by', 'updated_by', 'deleted_by'];
    protected $translatable = ['name'];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'xcore_user_subbranches', 'subbranch_id', 'user_id');
    }
}
