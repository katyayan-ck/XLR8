<?php

namespace App\Models\Utilities\EmpReporting;

use App\Models\BaseModel;
use App\Models\Traits\HasColumnTransformations;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeTopicReporter extends BaseModel
{
    use CrudTrait, HasFactory, HasColumnTransformations;

    protected $table = 'xlr8_utils_empreporting_reporters';

    protected $fillable = [
        'employee_code',
        'topic_code',
        'reporting_to_code',
        'scopes',
        'attributes',
        'priority',
        'is_active',
    ];

    protected array $columnTransformations = [
        'employee_code'     => ['trim', 'uppercase'],
        'topic_code'        => ['trim', 'uppercase'],
        'reporting_to_code' => ['trim', 'uppercase'],
    ];

    /**
     * Merge casts with BaseModel casts
     */
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'scopes'     => 'array',
            'attributes' => 'array',
            'priority'   => 'integer',
        ]);
    }

    // Relationships
    public function topic()
    {
        return $this->belongsTo(ReportingTopic::class, 'topic_code', 'code');
    }

    public function reportingTo()
    {
        return $this->belongsTo(\App\Models\Admin\Employee::class, 'reporting_to_code', 'code');
    }
}