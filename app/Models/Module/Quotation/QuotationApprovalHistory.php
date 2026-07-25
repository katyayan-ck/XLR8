<?php

namespace App\Models\Module\Quotation;

use App\Models\BaseModel;
use App\Traits\HasColumnTransformations;

class QuotationApprovalHistory extends BaseModel
{
    use HasColumnTransformations;

    protected $table = 'xlr8_quotation_approval_history';

    public $timestamps = false;

    protected $fillable = [
        'quotation_id',
        'approver_id',
        'approver_role',
        'action',
        'approval_level',
        'approved_amount',
        'remarks',
        'changes',
        'action_at',
        'created_by',
    ];

    protected $casts = [
        'changes'        => 'array',
        'approved_amount' => 'decimal:2',
        'action_at'      => 'datetime',
    ];

    protected function initializeCasts(): void
    {
        $this->casts = array_merge($this->casts, parent::getCasts());
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_id');
    }
}
