<?php

namespace App\Models;

class UserModel extends BaseModel
{
    protected $table = 'xcore_user_models';
    protected $fillable = ['user_id', 'model_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function model()
    {
        return $this->belongsTo(Model::class, 'model_id');
    }
}
