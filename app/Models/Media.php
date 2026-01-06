<?php 
	namespace App\Models;
	use Illuminate\Database\Eloquent\SoftDeletes;
	use DataTables,Auth;
	
	class Media extends BaseModel
	{
		/**
			* The database table used by the model.
			*
			* @var string
		*/
		use SoftDeletes;
		protected $table = 'bmpl_media_master';
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
	    protected function getCacheBaseTags(): array
		{
			return [
            'PlRTO_tag',
			];
		}
		public function Pricing()
		{
			return $this->belongsTo('App\Models\PriceList','pl_id');
		}
		
		public function Type()
		{
			return $this->belongsTo('App\Models\Statuses','type_id')->select('id','value');
		}
		
		
	}
