<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SheetProcessProgress extends Model
{
    protected $table = 'od_sheet_process_progress';
    protected $fillable = ['mapping_id', 'user_id', 'total_rows', 'processed_rows', 'status'];
    protected $casts = [
        'executed_at' => 'datetime',
        'status' => 'string',
    ];
}
