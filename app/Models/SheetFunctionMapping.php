<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SheetFunctionMapping extends Model
{
    use SoftDeletes;

    protected $table = 'od_sheet_function_mappings';
    protected $fillable = [
        'drive_id',
        'file_name',
        'sheet_name',
        'function_name',
        'sheet_type',
        'permission',
        'frequency',
        'description',
        'details',
        'file_url',
        'user_id'
    ];
}
