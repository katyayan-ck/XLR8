<?php

namespace App\Models\Vehicle;

use App\Models\BaseModel;
use App\Traits\HasColumnTransformations;
use Illuminate\Database\Eloquent\Builder;

class VehicleInsuranceBaseRule extends BaseModel
{
    use HasColumnTransformations;

    protected $table = 'xlr8_vehicle_insurance_base_rules';

    protected $fillable = [
        'model_code',
        'variant_code',
        'permit',
        'fuel_type',
        'wheels',
        'seating',
        'cc_range',
        'gvw_range',
        'plan',
        'od_factor',
        'od_surcharge',
        'od_discount_rate',
        'imt_23_rate',
        'tp_basic',
        'tp_per_passenger',
        'tp_legal_driver',
        'tp_non_fare_passenger',
        'tp_bi_fuel_kit',
        'is_active',
        'wef_date',
        'expired_on',
    ];

    protected $casts = [
        'od_factor' => 'decimal:6',
        'od_surcharge' => 'decimal:6',
        'od_discount_rate' => 'decimal:6',
        'imt_23_rate' => 'decimal:6',
        'tp_basic' => 'decimal:2',
        'tp_per_passenger' => 'decimal:2',
        'tp_legal_driver' => 'decimal:2',
        'tp_non_fare_passenger' => 'decimal:2',
        'tp_bi_fuel_kit' => 'decimal:2',
        'is_active' => 'boolean',
        'wef_date' => 'date',
        'expired_on' => 'date',
    ];

    // Merge with BaseModel casts
    protected function initializeCasts(): void
    {
        $this->casts = array_merge($this->casts, parent::getCasts());
    }

    // Scopes
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
                $q->where('variant_code', $variantCode)
                  ->orWhereNull('variant_code');
            });
        }
        
        return $query;
    }

    // Helper to get best matching rule
    public static function findBestMatch(array $criteria): ?self
    {
        // This will be expanded in PricingEngineService
        return self::query()
            ->active()
            ->forVehicle($criteria['model_code'], $criteria['variant_code'] ?? null)
            ->where('permit', $criteria['permit'])
            ->where('fuel_type', $criteria['fuel_type'])
            ->orderByRaw('CASE WHEN variant_code IS NOT NULL THEN 1 ELSE 2 END')
            ->first();
    }
}