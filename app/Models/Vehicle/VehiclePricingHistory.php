<?php

namespace App\Models\Vehicle;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;

class VehiclePricingHistory extends BaseModel
{
    protected $table = 'xlr8_vehicle_pricing_history';

    protected $fillable = [
        'pricing_id',
        'model_code',
        'variant_code',
        'wef_date',
        'expired_on',
        'pricing_snapshot',
        'changed_by',
        'change_reason',
    ];

    protected $casts = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->casts = array_merge($this->casts, [
            'wef_date'         => 'date',
            'expired_on'       => 'date',
            'pricing_snapshot' => 'array',
        ]);
    }

    // ==================== RELATIONSHIPS ====================

    public function pricing()
    {
        return $this->belongsTo(VehiclePricing::class, 'pricing_id');
    }

    public function changedByUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'changed_by');
    }

    // ==================== SCOPES ====================

    public function scopeForVehicle(Builder $query, string $modelCode, ?string $variantCode = null): Builder
    {
        $query->where('model_code', $modelCode);

        if ($variantCode) {
            $query->where('variant_code', $variantCode);
        }

        return $query;
    }

    public function scopeRecent(Builder $query, int $limit = 10): Builder
    {
        return $query->orderByDesc('created_at')->limit($limit);
    }

    // ==================== ACCESSORS ====================

    public function getOldExShowroomAttribute(): ?float
    {
        return $this->pricing_snapshot['ex_showroom_price'] ?? null;
    }

    public function getNewExShowroomAttribute(): ?float
    {
        return data_get($this->pricing_snapshot, 'new.ex_showroom_price');
    }
}