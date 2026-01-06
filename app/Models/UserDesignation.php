<?php

namespace App\Models;

class UserDesignation extends BaseModel
{
	protected $table = 'xcore_user_designations';
	protected $fillable = ['user_id', 'designation_id'];

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}

	public function designation()
	{
		return $this->belongsTo(Designation::class, 'designation_id');
	}
}
