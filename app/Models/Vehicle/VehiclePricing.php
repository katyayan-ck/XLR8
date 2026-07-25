<?php

namespace App\Models\Vehicle;

use App\Models\BaseModel;
use App\Traits\HasColumnTransformations;
use Illuminate\Database\Eloquent\Builder;

class VehiclePricing extends BaseModel
{
    use HasColumnTransformations;

    protected $table = 'xlr8_vehicle_pricing';

    protected $fillable = [
        'model_code',
        'variant_code',
        'wef_date',
        'expired_on',
        'is_active',
        'ex_showroom_price',
        'assessable_value_with_freight',
        'gst_percent',
        'gst_amount',
        'mm_invoice_amount',
        'oem_scheme',
        'dealer_margin',
    ];

    protected $casts = [
        'wef_date'                      => 'date',
        'expired_on'                    => 'date',
        'is_active'                     => 'boolean',
        'ex_showroom_price'             => 'decimal:2',
        'assessable_value_with_freight' => 'decimal:2',
        'gst_percent'                   => 'decimal:2',
        'gst_amount'                    => 'decimal:2',
        'mm_invoice_amount'             => 'decimal:2',
        'oem_scheme'                    => 'decimal:2',
        'dealer_margin'                 => 'decimal:2',
    ];

    protected function initializeCasts(): void
    {
        $this->casts = array_merge($this->casts, parent::getCasts());
    }

    protected array $columnTransformations = [
        'model_code'   => 'uppercase_alphanumeric_dash_underscore',
        'variant_code' => 'uppercase_alphanumeric_dash_underscore',
    ];

    // ==================== RELATIONSHIPS ====================

    public function history()
    {
        return $this->hasMany(VehiclePricingHistory::class, 'pricing_id');
    }

    // ==================== SCOPES ====================

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
                     ->whereNull('expired_on')
                     ->where('wef_date', '<=', now()->toDateString());
    }

    public static function getActivePricing(string $modelCode, ?string $variantCode = null): ?self
    {
        $query = self::query()->active()->where('model_code', $modelCode);

        if ($variantCode) {
            $query->where('variant_code', $variantCode);
        }

        return $query->orderByDesc('wef_date')->first();
    }
}