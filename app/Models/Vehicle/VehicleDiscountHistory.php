<?php

namespace App\Models\Vehicle;

use App\Models\BaseModel;
use App\Traits\HasColumnTransformations;

class VehicleDiscountHistory extends BaseModel
{
    use HasColumnTransformations;

    protected $table = 'xlr8_vehicle_discount_history';

    public $timestamps = false;

    protected $fillable = [
        'discount_id',
        'old_data',
        'new_data',
        'change_type',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];

    protected function initializeCasts(): void
    {
        $this->casts = array_merge($this->casts, parent::getCasts());
    }

    public function discount()
    {
        return $this->belongsTo(VehicleDiscount::class, 'discount_id');
    }

    /**
     * Record a change in discount configuration
     */
    public static function recordChange(int $discountId, array $oldData, array $newData, string $changeType, ?string $remarks = null, ?int $userId = null): self
    {
        return self::create([
            'discount_id' => $discountId,
            'old_data' => $oldData,
            'new_data' => $newData,
            'change_type' => $changeType,
            'remarks' => $remarks,
            'created_by' => $userId,
        ]);
    }
}