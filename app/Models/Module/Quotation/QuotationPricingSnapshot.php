<?php

namespace App\Models\Module\Quotation;

use App\Models\BaseModel;
use App\Traits\HasColumnTransformations;

class QuotationPricingSnapshot extends BaseModel
{
    use HasColumnTransformations;

    protected $table = 'xlr8_quotation_pricing_snapshots';

    protected $fillable = [
        'quotation_id',
        'snapshot_type',
        'pricing_data',
        'discount_bifurcation',
        'withheld_discounts',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'pricing_data'         => 'array',
        'discount_bifurcation' => 'array',
        'withheld_discounts'   => 'array',
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
