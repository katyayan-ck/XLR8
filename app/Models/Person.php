<?php

namespace App\Models;

class Person extends BaseModel
{
	protected $table = 'bmpl_person_master';
	protected $fillable = ['id', 'firstname', 'mobile', 'email'];

	public function user()
	{
		return $this->hasOne(User::class, 'person_id');
	}
}
