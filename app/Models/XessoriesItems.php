<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use DataTables, Auth;

class XessoriesItems extends BaseModel

{

    /**

     * The database table used by the model.

     *

     * @var string

     */

    use SoftDeletes;

    protected $table = 'xcelr8_accessories_items';
    protected $transformations = [
        'name' => 'strtoupper',

        // 'col2' => function ($value) { // Custom transformation to trim and replace spaces
        //     return str_replace(' ', '_', trim($value));
        // },
    ];

    protected $validations = [
        //'email' => ['regex:/^.+@.+\..+$/'], // Ensure email matches a simple pattern
    ];


    /**

     * The attributes to be fillable from the model.

     *

     * A dirty hack to allow fields to be fillable by calling empty fillable array

     *

     * @var array

     */



    protected $fillable = [];

    protected $guarded = ['id'];

    /**

     * The attributes excluded from the model's JSON form.

     *

     * @var array

     */

    // public function state()

    // 	{

    // 		return $this->belongsTo('App\Models\States','state_id');

    // 	}



    // public function users()

    // 	{

    // 		return $this->hasMany('App\Models\BranchUser','branch_id');

    // 	}


    public function child()
    {
        return $this->hasMany('App\Models\Xessories', 'item_id');
    }
}
