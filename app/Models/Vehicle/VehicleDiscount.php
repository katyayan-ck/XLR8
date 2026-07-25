<?php

namespace App\Models\Vehicle;

use App\Models\BaseModel;
use App\Traits\HasColumnTransformations;
use Illuminate\Database\Eloquent\Builder;

class VehicleDiscount extends BaseModel
{
    use HasColumnTransformations;

    protected $table = 'xlr8_vehicle_discounts';

    protected $fillable = [
        'model_code',
        'variant_code',
        'discount_type',
        'discount_category',
        'name',
        'description',
        'oem_share',
        'dealer_share',
        'total_discount',
        'conditions',
        'is_conditional',
        'linked_to',
        'requires_approval',
        'is_active',
        'wef_date',
        'expired_on',
    ];

    protected $casts = [
        'oem_share'      => 'decimal:2',
        'dealer_share'   => 'decimal:2',
        'total_discount' => 'decimal:2',
        'conditions'     => 'array',
        'is_conditional' => 'boolean',
        'requires_approval' => 'boolean',
        'is_active'      => 'boolean',
        'wef_date'       => 'date',
        'expired_on'     => 'date',
    ];

    protected function initializeCasts(): void
    {
        $this->casts = array_merge($this->casts, parent::getCasts());
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
                     ->whereNull('expired_on')
                     ->where('wef_date', '<=', now()->toDateString());
    }

    public function scopeForVehicle(Builder $query, string $modelCode, ?string $variantCode = null): Builder
    {
        $query->where('model_code', $modelCode);

        if ($variantCode) {
            $query->where(function ($q) use ($variantCode) {
                $q->where('variant_code', $variantCode)->orWhereNull('variant_code');
            });
        }

        return $query;
    }
}