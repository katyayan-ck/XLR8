<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use DataTables, Auth;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Models\Traits\HasHashedMediaTrait;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\MediaCollections\File;


class Documents extends BaseModel implements HasMedia
{
    //use QueryCacheable;
    //public $cacheFor = 60*60*24;
    use InteractsWithMedia;
    protected $table = 'bmpl_documents';
    protected $fillable = [];
    protected $guarded = ['id'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')

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

        $this->addMediaCollection('pdf');
    }

    protected function getCacheBaseTags(): array
    {
        return [
            'Doc_tag',
        ];
    }
}
