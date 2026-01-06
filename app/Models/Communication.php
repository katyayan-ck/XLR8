<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use DataTables, Auth;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Models\Traits\HasHashedMediaTrait;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\MediaCollections\File;

class Communication extends BaseModel  implements HasMedia
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    use SoftDeletes;
    use InteractsWithMedia;
    protected $table = 'bmpl_communication_master';
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
        return $this->belongsTo('App\Models\PriceList', 'pl_id');
    }

    public function Type()
    {
        return $this->belongsTo('App\Models\Statuses', 'type_id')->select('id', 'value');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('comm-docs')

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
    }
}
