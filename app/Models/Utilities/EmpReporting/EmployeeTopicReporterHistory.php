<?php

namespace App\Models\Utilities\EmpReporting;

use App\Models\BaseModel;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeTopicReporterHistory extends BaseModel
{
    use CrudTrait, HasFactory;

    protected $table = 'xlr8_utils_empreporting_histories';

    protected $fillable = [
        'employee_topic_reporter_id',
        'employee_code',
        'topic_code',
        'field',
        'old_value',
        'new_value',
        'changed_by',
        'reason',
    ];

    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'old_value' => 'array',
            'new_value' => 'array',
        ]);
    }
}