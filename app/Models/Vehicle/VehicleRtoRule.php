<?php

namespace App\Models\Vehicle;

use App\Models\BaseModel;
use App\Traits\HasColumnTransformations;
use Illuminate\Database\Eloquent\Builder;

class VehicleRtoRule extends BaseModel
{
    use HasColumnTransformations;

    protected $table = 'xlr8_vehicle_rto_rules';

    protected $fillable = [
        'permit',
        'wheels',
        'reg_type',
        'body_type',
        'gvw_range',
        'seater',
        'fuel_type',
        'cc_range',
        'tax_factor',
        'tax_slab',
        'surcharge',
        'hypothecation',
        'green_tax',
        'registration_fee',
        'duplicate_tax_card',
        'fitness',
        'penalty',
        'is_active',
        'wef_date',
        'expired_on',
    ];

    protected $casts = [
        'tax_factor' => 'decimal:6',
        'surcharge' => 'decimal:2',
        'hypothecation' => 'decimal:2',
        'green_tax' => 'decimal:2',
        'registration_fee' => 'decimal:2',
        'duplicate_tax_card' => 'decimal:2',
        'fitness' => 'decimal:2',
        'penalty' => 'decimal:2',
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

    public function scopeForPermit(Builder $query, string $permit): Builder
    {
        return $query->where('permit', $permit);
    }

    /**
     * Find the best matching RTO rule based on vehicle attributes.
     * Priority: Most specific match first.
     */
    public static function findBestMatch(array $criteria): ?self
    {
        $query = self::query()->active()->forPermit($criteria['permit']);

        // Apply filters with decreasing specificity
        if (!empty($criteria['wheels'])) {
            $query->where(function ($q) use ($criteria) {
                $q->where('wheels', $criteria['wheels'])->orWhereNull('wheels');
            });
        }

        if (!empty($criteria['fuel_type'])) {
            $query->where(function ($q) use ($criteria) {
                $q->where('fuel_type', $criteria['fuel_type'])->orWhereNull('fuel_type');
            });
        }

        if (!empty($criteria['gvw_range'])) {
            $query->where(function ($q) use ($criteria) {
                $q->where('gvw_range', $criteria['gvw_range'])->orWhereNull('gvw_range');
            });
        }

        if (!empty($criteria['seater'])) {
            $query->where(function ($q) use ($criteria) {
                $q->where('seater', $criteria['seater'])->orWhereNull('seater');
            });
        }

        if (!empty($criteria['cc_range'])) {
            $query->where(function ($q) use ($criteria) {
                $q->where('cc_range', $criteria['cc_range'])->orWhereNull('cc_range');
            });
        }

        if (!empty($criteria['reg_type'])) {
            $query->where(function ($q) use ($criteria) {
                $q->where('reg_type', $criteria['reg_type'])->orWhereNull('reg_type');
            });
        }

        // Order by specificity (non-null values first)
        return $query->orderByRaw('
            (CASE WHEN wheels IS NOT NULL THEN 1 ELSE 2 END) +
            (CASE WHEN fuel_type IS NOT NULL THEN 1 ELSE 2 END) +
            (CASE WHEN gvw_range IS NOT NULL THEN 1 ELSE 2 END) +
            (CASE WHEN seater IS NOT NULL THEN 1 ELSE 2 END)
        ')->first();
    }
}