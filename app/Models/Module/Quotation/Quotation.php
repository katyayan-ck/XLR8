<?php
namespace App\Models\Module\Quotation;

use App\Models\BaseModel;
use App\Traits\HasColumnTransformations;
use Illuminate\Database\Eloquent\Builder;

class Quotation extends BaseModel
{
    use HasColumnTransformations;

    protected $table = 'xlr8_crm_quotations';

    protected $fillable = [
        'enquiry_code',
        'enquiry_id',
        'model_code',
        'variant_code',
        'colour_name',
        'person_code',
        'person_id',
        'quote_type',
        'status',
        'current_approval_level',
        'latest_snapshot_id',
        'ex_showroom_price',
        'on_road_price',
        'invoice_value',
        'total_discount',
        'sales_consultant_id',
        'branch_id',
        'customer_remarks',
        'internal_remarks',
    ];

    protected $casts = [
        'ex_showroom_price' => 'decimal:2',
        'on_road_price'     => 'decimal:2',
        'invoice_value'     => 'decimal:2',
        'total_discount'    => 'decimal:2',
        'current_approval_level' => 'integer',
    ];

    protected function initializeCasts(): void
    {
        $this->casts = array_merge($this->casts, parent::getCasts());
    }

    // ==================== RELATIONSHIPS ====================

    public function latestSnapshot()
    {
        return $this->hasOne(QuotationPricingSnapshot::class, 'quotation_id')->latestOfMany();
    }

    public function approvalHistory()
    {
        return $this->hasMany(QuotationApprovalHistory::class, 'quotation_id');
    }

    public function pricingSnapshots()
    {
        return $this->hasMany(QuotationPricingSnapshot::class, 'quotation_id');
    }

    // ==================== SCOPES ====================

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('deleted_at');
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeForSalesConsultant(Builder $query, int $userId): Builder
    {
        return $query->where('sales_consultant_id', $userId);
    }

    // ==================== HELPERS ====================

    public function isModifiable(): bool
    {
        return in_array($this->status, ['draft', 'under_process', 'modified']);
    }
}
