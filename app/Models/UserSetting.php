<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    protected $fillable = ['user_id', 'log_process_result'];

    protected $casts = [
        'log_process_result' => 'boolean',
    ];
}
