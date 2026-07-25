<?php

namespace App\Models\Vehicle;

use App\Models\BaseModel;
use App\Traits\HasColumnTransformations;
use Illuminate\Database\Eloquent\Builder;

class VehicleInsuranceDefault extends BaseModel
{
    use HasColumnTransformations;

    protected $table = 'xlr8_vehicle_insurance_defaults';

    protected $fillable = [
        'model_code',
        'permit',
        'default_company',
        'company_priority_2',
        'company_priority_3',
        'is_active',
        'wef_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'wef_date' => 'date',
    ];

    protected function initializeCasts(): void
    {
        $this->casts = array_merge($this->casts, parent::getCasts());
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForModelAndPermit(Builder $query, string $modelCode, string $permit): Builder
    {
        return $query->where('model_code', $modelCode)
                     ->where('permit', $permit);
    }

    /**
     * Get default insurance companies for a vehicle + permit
     */
    public static function getDefaultCompanies(string $modelCode, string $permit): array
    {
        $record = self::query()
            ->active()
            ->forModelAndPermit($modelCode, $permit)
            ->first();

        if (!$record) {
            return ['USGI']; // Fallback default
        }

        $companies = [$record->default_company];
        
        if ($record->company_priority_2) {
            $companies[] = $record->company_priority_2;
        }
        
        if ($record->company_priority_3) {
            $companies[] = $record->company_priority_3;
        }

        return array_unique($companies);
    }
}