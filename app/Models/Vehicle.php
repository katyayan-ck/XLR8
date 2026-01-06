<?php
	
	namespace App\Models;
	
	use Illuminate\Database\Eloquent\SoftDeletes;
	use DataTables,Auth;
	use Spatie\MediaLibrary\HasMedia;
	use Spatie\MediaLibrary\InteractsWithMedia;
	use App\Models\Traits\HasHashedMediaTrait;
	use Spatie\MediaLibrary\MediaCollections\Models\Media;
	use Spatie\MediaLibrary\MediaCollections\File;
	
	
	class Vehicle extends BaseModel implements HasMedia
	{
		//use QueryCacheable;
		//public $cacheFor = 60*60*24;
		use InteractsWithMedia;
		protected $table = 'bmpl_vehicle_master';
		protected $fillable = [];
		protected $guarded = ['id'];
		
		public function registerMediaCollections() : void 
		{
			$this->addMediaCollection('front')
			->acceptsFile(function (File $file) {
				return $file->mimeType === 'image/jpeg';
			})	
			->registerMediaConversions(function (Media $media) {
				$this->addMediaConversion('thumb250')
				->width(250)
				->height(250)
				->quality(70);
				
				$this->addMediaConversion('thumb100')
				->width(100)
				->height(100)
				->quality(70);
			});
			$this->addMediaCollection('front')
			->acceptsFile(function (File $file) {
				return $file->mimeType === 'image/jpeg';
			})	
			->registerMediaConversions(function (Media $media) {
				$this->addMediaConversion('thumb250')
				->width(250)
				->height(250)
				->quality(70);
				
				$this->addMediaConversion('thumb100')
				->width(100)
				->height(100)
				->quality(70);
			});
			$this->addMediaCollection('back')
			->acceptsFile(function (File $file) {
				return $file->mimeType === 'image/jpeg';
			})	
			->registerMediaConversions(function (Media $media) {
				$this->addMediaConversion('thumb250')
				->width(250)
				->height(250)
				->quality(70);
				
				$this->addMediaConversion('thumb100')
				->width(100)
				->height(100)
				->quality(70);
			});
			$this->addMediaCollection('right')
			->acceptsFile(function (File $file) {
				return $file->mimeType === 'image/jpeg';
			})	
			->registerMediaConversions(function (Media $media) {
				$this->addMediaConversion('thumb250')
				->width(250)
				->height(250)
				->quality(70);
				
				$this->addMediaConversion('thumb100')
				->width(100)
				->height(100)
				->quality(70);
			});
			$this->addMediaCollection('left')
			->acceptsFile(function (File $file) {
				return $file->mimeType === 'image/jpeg';
			})	
			->registerMediaConversions(function (Media $media) {
				$this->addMediaConversion('thumb250')
				->width(250)
				->height(250)
				->quality(70);
				
				$this->addMediaConversion('thumb100')
				->width(100)
				->height(100)
				->quality(70);
			});
			$this->addMediaCollection('pdf')
			->acceptsFile(function (File $file) {
				return $file->mimeType === 'application/pdf';
			});
		}

		protected function getCacheBaseTags(): array
		{
			return [
            'Vehicle_tag',
			];
		}
		
		public function child()
		{
			return $this->hasMany('App\Models\MakeModel','parent');
		}
		
		
		public function parentid()
		{
			return $this->belongsTo('App\Models\MakeModel','parent')->select('id','name');
		}
		
		
		public function bndp()
		{
			return $this->hasMany('App\Models\BNDP','vehicle_id');
			$rtrec = null;
			$bnd = $this->hasMany('App\Models\BNDP','vehicle_id');
			foreach($bnd as $bndp)
			{
				if($rtrec == null)
				$rtrec = $bndp;
				else
				{
					if($rtrec->wefdate < $bndp->wefdate)
					$rtrec  = $bndp;
				}
			}
			return $rtrec;
		}
		public function scheme()
		{
			return $this->hasMany('App\Models\SplScheme','vehicle_id');
		}
	}
	
	
