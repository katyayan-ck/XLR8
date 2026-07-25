<?php

namespace App\Models\Vehicle;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;

class VehicleAddonHistory extends BaseModel
{
    protected $table = 'xlr8_vehicle_addon_history';

    protected $fillable = [
        'addon_id',
        'model_code',
        'variant_code',
        'addon_type',
        'old_data',
        'new_data',
        'changed_by',
        'change_reason',
    ];

    protected $casts = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->casts = array_merge($this->casts, [
            'old_data' => 'array',
            'new_data' => 'array',
        ]);
    }

    // ==================== RELATIONSHIPS ====================

    public function addon()
    {
        return $this->belongsTo(VehicleAddon::class, 'addon_id');
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

    public function scopeByType(Builder $query, string $addonType): Builder
    {
        return $query->where('addon_type', $addonType);
    }

    public function scopeRecent(Builder $query, int $limit = 20): Builder
    {
        return $query->orderByDesc('created_at')->limit($limit);
    }

    // ==================== ACCESSORS ====================

    /**
     * Get old amount from snapshot
     */
    public function getOldAmountAttribute(): ?float
    {
        return $this->old_data['amount'] ?? null;
    }

    /**
     * Get new amount from snapshot
     */
    public function getNewAmountAttribute(): ?float
    {
        return $this->new_data['amount'] ?? null;
    }

    /**
     * Get the difference in amount (new - old)
     */
    public function getAmountDifferenceAttribute(): ?float
    {
        if ($this->old_amount === null || $this->new_amount === null) {
            return null;
        }

        return $this->new_amount - $this->old_amount;
    }

    /**
     * Check if the amount was increased
     */
    public function getWasIncreasedAttribute(): bool
    {
        return $this->amount_difference > 0;
    }

    /**
     * Check if the amount was decreased
     */
    public function getWasDecreasedAttribute(): bool
    {
        return $this->amount_difference < 0;
    }

    // ==================== HELPER METHODS ====================

    /**
     * Create a history record
     */
    public static function recordChange(
        int $addonId,
        array $oldData,
        array $newData,
        ?int $changedBy = null,
        ?string $reason = null
    ): self {
        return self::create([
            'addon_id'      => $addonId,
            'model_code'    => $newData['model_code'] ?? $oldData['model_code'] ?? null,
            'variant_code'  => $newData['variant_code'] ?? $oldData['variant_code'] ?? null,
            'addon_type'    => $newData['addon_type'] ?? $oldData['addon_type'] ?? null,
            'old_data'      => $oldData,
            'new_data'      => $newData,
            'changed_by'    => $changedBy ?? auth()->id(),
            'change_reason' => $reason,
        ]);
    }
}