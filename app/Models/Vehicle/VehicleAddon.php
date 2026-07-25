<?php

namespace App\Models\Vehicle;

use App\Models\BaseModel;
use App\Traits\HasColumnTransformations;
use Illuminate\Database\Eloquent\Builder;

class VehicleAddon extends BaseModel
{
    use HasColumnTransformations;

    protected $table = 'xlr8_vehicle_addons';

    protected $fillable = [
        'model_code',
        'variant_code',
        'addon_type',
        'scheme_name',
        'tenure_years',
        'name',
        'description',
        'amount',
        'amount_type',
        'oem_share',
        'dealer_share',
        'conditions',
        'is_default',
        'is_active',
        'wef_date',
        'expired_on',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'oem_share' => 'decimal:2',
        'dealer_share' => 'decimal:2',
        'conditions' => 'array',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'tenure_years' => 'integer',
        'wef_date' => 'date',
        'expired_on' => 'date',
    ];

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

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('addon_type', $type);
    }

    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    // Helper: Get default RSA (1 Year)
    public static function getDefaultRsa(string $modelCode, ?string $variantCode = null): ?self
    {
        return self::query()
            ->active()
            ->forVehicle($modelCode, $variantCode)
            ->ofType('rsa')
            ->where('tenure_years', 1)
            ->first();
    }

    // Helper: Get default Shield
    public static function getDefaultShield(string $modelCode, ?string $variantCode = null): ?self
    {
        return self::query()
            ->active()
            ->forVehicle($modelCode, $variantCode)
            ->ofType('shield')
            ->default()
            ->first();
    }
}