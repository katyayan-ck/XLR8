<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use DataTables, Auth;

class XVehicleSpecifications extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    use SoftDeletes;
    protected $table = 'xcelr8_vh_specs';

    /**
     * The attributes to be fillable from the model.
     *
     * A dirty hack to allow fields to be fillable by calling empty fillable array
     *
     * @var array
     */

    protected $fillable = [];
    protected $guarded = ['id'];
}
