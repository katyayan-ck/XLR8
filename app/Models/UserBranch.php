<?php

namespace App\Models;

class UserBranch extends BaseModel
{
	protected $table = 'xcore_user_branches';
	protected $fillable = ['user_id', 'branch_id'];

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}

	public function branch()
	{
		return $this->belongsTo(Branch::class, 'branch_id');
	}
}
