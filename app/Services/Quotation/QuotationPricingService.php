<?php

namespace App\Services\Quotation;

use App\Services\Vehicle\PricingEngineService;

class QuotationPricingService
{
    protected PricingEngineService $pricingEngine;

    public function __construct(PricingEngineService $pricingEngine)
    {
        $this->pricingEngine = $pricingEngine;
    }

    public function buildQuotationPricing(array $data, array $options = []): array
    {
        $modelCode   = $data['model_code'];
        $variantCode = $data['variant_code'] ?? null;
        $permit      = $data['permit'] ?? 'Private';

        $pricing = $this->pricingEngine->getActivePricing($modelCode, $variantCode);

        $insuranceCompanies = $this->pricingEngine->getDefaultInsuranceCompanies($modelCode, $permit);
        $baseRule = $this->pricingEngine->getInsuranceBaseRule([
            'model_code'   => $modelCode,
            'variant_code' => $variantCode,
            'permit'       => $permit,
            'fuel_type'    => $data['fuel_type'] ?? 'DIESEL',
        ]);

        $baseInsurance = $baseRule 
            ? $this->pricingEngine->calculateBaseInsurancePremium($baseRule, $pricing?->ex_showroom_price ?? 0)
            : [];

        $applicableDiscounts = $this->pricingEngine->getApplicableDiscounts($modelCode, $variantCode);
        $calculatedDiscounts = [];

        foreach ($applicableDiscounts as $discountData) {
            $discountModel = new \App\Models\Vehicle\VehicleDiscount($discountData);
            $calculatedDiscounts[] = $this->pricingEngine->calculateDiscount($discountModel, [
                'invoice_value' => $pricing?->ex_showroom_price ?? 0
            ]);
        }

        $withheld = $this->pricingEngine->getWithheldDiscounts($calculatedDiscounts, $options);
        $bifurcation = $this->pricingEngine->applyDiscountBifurcation($calculatedDiscounts);

        return [
            'ex_showroom'     => $pricing?->ex_showroom_price ?? 0,
            'dealer_charges'  => $this->pricingEngine->getDealerCharges($data['segment'] ?? 'PV', $modelCode),
            'insurance'       => [
                'company'       => $insuranceCompanies[0] ?? 'USGI',
                'base_premium'  => $baseInsurance,
            ],
            'discounts'       => [
                'applied'     => $calculatedDiscounts,
                'withheld'    => $withheld,
                'bifurcation' => $bifurcation,
            ],
            'tcs_applicable'  => ($pricing?->ex_showroom_price ?? 0) >= 1000000,
            'calculated_at'   => now()->toDateTimeString(),
        ];
    }

    public function recalculate(array $currentPricing, array $changes): array
    {
        if (isset($changes['insurance_company'])) {
            $currentPricing['insurance']['company'] = $changes['insurance_company'];
        }

        if (isset($changes['rsa_selected'])) {
            $currentPricing['discounts']['withheld'] = array_filter(
                $currentPricing['discounts']['withheld'] ?? [],
                fn($d) => $d['linked_to'] !== 'rsa'
            );
        }

        $currentPricing['calculated_at'] = now()->toDateTimeString();
        return $currentPricing;
    }
}