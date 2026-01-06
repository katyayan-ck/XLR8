<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use DataTables, Auth;
//use Rennokki\QueryCache\Traits\QueryCacheable;
namespace App\Models;


class EnumCols extends BaseModel
{
    //use QueryCacheable;
    public $cacheFor = 60 * 60 * 24;
    protected $table = 'bmpl_enum_columns';

    protected $fillable = [];
    protected $guarded = ['id'];

    protected function getCacheBaseTags(): array
    {
        return [
            'Enumrator_tag',
        ];
    }
}
