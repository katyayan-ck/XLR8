<?php

namespace App\Models;

use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Traits\HasColumnRules;
use Spatie\Translatable\HasTranslations;

class BaseModel extends Model implements HasMedia
{
	use SoftDeletes, HasColumnRules, HasTranslations, InteractsWithMedia;


	protected static $flushCacheOnUpdate = true;

	protected $guarded = ['id', 'updated_at', '_token', '_method'];
	protected $dates = ['deleted_at', 'published_at'];
	protected $translatable = [];

	public function registerMediaCollections(): void
	{
		$this->addMediaCollection('default');
	}

	protected static function boot()
	{
		parent::boot();

		static::creating(function ($table) {
			$table->created_by = $table->created_by ?? (Auth::id() ?? 1);
			$table->created_at = $table->created_at ?? Carbon::now();
		});

		static::updating(function ($table) {
			$table->updated_by = $table->updated_by ?? (Auth::id() ?? 1);
		});

		static::saving(function ($table) {
			$table->updated_by = $table->updated_by ?? (Auth::id() ?? 1);
		});

		static::deleting(function ($table) {
			$table->deleted_by = $table->deleted_by ?? (Auth::id() ?? 1);
			$table->save();
		});
	}
}
