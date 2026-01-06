<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SheetProcessHistory extends Model
{
    protected $table = 'od_sheet_process_history';
    protected $fillable = [
        'mapping_id',
        'user_id',
        'status',
        'remarks',
        'executed_at',
    ];
    protected $casts = [
        'executed_at' => 'datetime',
        'status' => 'string',
    ];
}
