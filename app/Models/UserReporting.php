<?php

namespace App\Models;

class UserReporting extends BaseModel
{
    protected $table = 'xcelr8_user_reporting';
    protected $fillable = [
        'emp_code',
        'reports_to',
        'segment',
        'model',
        'topic_id',
        'wef',
        'wet',
        'status',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
    protected $translatable = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'emp_code', 'emp_code');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'reports_to', 'emp_code');
    }

    public function topic()
    {
        return $this->belongsTo(EnumMaster::class, 'topic_id');
    }
}
