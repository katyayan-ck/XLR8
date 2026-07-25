<?php

namespace App\Models\Vehicle;

use App\Models\BaseModel;
use App\Traits\HasColumnTransformations;
use Illuminate\Database\Eloquent\Builder;

class VehicleInsuranceAddonRate extends BaseModel
{
    use HasColumnTransformations;

    protected $table = 'xlr8_vehicle_insurance_addon_rates';

    protected $fillable = [
        'insurance_company',
        'permit',
        'addon_slug',
        'addon_name',
        'rate_type',
        'rate_value',
        'applies_on',
        'conditions',
        'is_active',
        'wef_date',
        'expired_on',
    ];

    protected $casts = [
        'rate_value' => 'decimal:4',
        'conditions' => 'array',
        'is_active' => 'boolean',
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

    public function scopeForCompanyAndPermit(Builder $query, string $company, string $permit): Builder
    {
        return $query->where('insurance_company', $company)
                     ->where('permit', $permit);
    }

    public function scopeBySlug(Builder $query, string $slug): Builder
    {
        return $query->where('addon_slug', $slug);
    }

    // Helper: Get all active addons for a company + permit
    public static function getActiveAddons(string $company, string $permit): array
    {
        return self::query()
            ->active()
            ->forCompanyAndPermit($company, $permit)
            ->get()
            ->keyBy('addon_slug')
            ->toArray();
    }
}