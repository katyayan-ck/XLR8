<?php

namespace App\Models\Utilities\EmpReporting;

use App\Models\BaseModel;
use App\Models\Traits\HasColumnTransformations;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class ReportingTopic extends BaseModel
{
    use CrudTrait, HasFactory, HasColumnTransformations;

    protected $table = 'xlr8_utils_empreporting_topics';

    protected $fillable = [
        'code',
        'name',
        'description',
        'required_attributes',
        'is_active',
    ];

    protected array $columnTransformations = [
        'code'        => ['trim', 'uppercase_alphanumeric_dash_underscore'],
        'name'        => ['trim_spaces', 'title_case'],
        'description' => ['trim_spaces'],
    ];

    /**
     * Merge casts with BaseModel casts
     */
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'required_attributes' => 'array',
        ]);
    }

    // Relationships
    public function reporters()
    {
        return $this->hasMany(EmployeeTopicReporter::class, 'topic_code', 'code');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}