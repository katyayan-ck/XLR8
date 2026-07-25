<?php

namespace App\Helpers;

use App\Models\Quotation\Quotation;
use App\Services\Quotation\QuotationPricingService;

class QuotationHelper
{
    protected QuotationPricingService $pricingService;

    public function __construct(QuotationPricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    public function createQuotation(array $data): Quotation
    {
        $pricingData = $this->pricingService->buildQuotationPricing($data);

        $quotation = Quotation::create([
            'enquiry_id'           => $data['enquiry_id'] ?? null,
            'model_code'           => $data['model_code'],
            'variant_code'         => $data['variant_code'] ?? null,
            'person_id'            => $data['person_id'] ?? null,
            'quote_type'           => $data['quote_type'] ?? 'standard',
            'status'               => 'draft',
            'current_approval_level' => 1,
            'sales_consultant_id'  => $data['sales_consultant_id'] ?? auth()->id(),
            'ex_showroom_price'    => $pricingData['ex_showroom'] ?? 0,
            'on_road_price'        => $pricingData['on_road_price'] ?? 0,
            'invoice_value'        => $pricingData['invoice_value'] ?? 0,
            'total_discount'       => $pricingData['total_discount'] ?? 0,
        ]);

        // Save snapshot
        $snapshotData = $this->pricingService->prepareSnapshotData($pricingData);
        $snapshot = $quotation->pricingSnapshots()->create($snapshotData);
        $quotation->update(['latest_snapshot_id' => $snapshot->id]);

        return $quotation;
    }

    public function processApproval(Quotation $quotation, string $action, int $approverId, ?string $remarks = null): bool
    {
        $validActions = ['approved', 'modified', 'escalated', 'rejected'];
        if (!in_array($action, $validActions)) return false;

        $quotation->approvalHistory()->create([
            'approver_id'    => $approverId,
            'action'         => $action,
            'approval_level' => $quotation->current_approval_level,
            'remarks'        => $remarks,
        ]);

        match ($action) {
            'approved'  => $quotation->update(['status' => 'approved', 'current_approval_level' => $quotation->current_approval_level + 1]),
            'modified'  => $quotation->update(['status' => 'modified']),
            'escalated' => $quotation->update(['status' => 'escalated', 'current_approval_level' => $quotation->current_approval_level + 1]),
            'rejected'  => $quotation->update(['status' => 'rejected']),
        };

        return true;
    }
}