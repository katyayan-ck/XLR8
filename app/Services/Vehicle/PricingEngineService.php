<?php

namespace App\Services\Vehicle;

use App\Models\Vehicle\VehiclePricing;
use App\Models\Vehicle\VehicleInsuranceBaseRule;
use App\Models\Vehicle\VehicleInsuranceAddonRate;
use App\Models\Vehicle\VehicleInsuranceDefault;
use App\Models\Vehicle\VehicleRtoRule;
use App\Models\Vehicle\VehicleAddon;
use App\Models\Vehicle\VehicleDiscount;
use Illuminate\Support\Facades\Cache;

class PricingEngineService
{
    // ==================== PRICING ====================

    public function getActivePricing(string $modelCode, ?string $variantCode = null): ?VehiclePricing
    {
        $cacheKey = $variantCode 
            ? "pricing:active:{$modelCode}:{$variantCode}" 
            : "pricing:active:{$modelCode}";

        return Cache::remember($cacheKey, 21600, function () use ($modelCode, $variantCode) {
            return VehiclePricing::getActivePricing($modelCode, $variantCode);
        });
    }

    // ==================== INSURANCE ====================

    public function getDefaultInsuranceCompanies(string $modelCode, string $permit): array
    {
        $cacheKey = "insurance:defaults:{$modelCode}:{$permit}";

        return Cache::remember($cacheKey, 21600, function () use ($modelCode, $permit) {
            return VehicleInsuranceDefault::getDefaultCompanies($modelCode, $permit);
        });
    }

    public function getInsuranceBaseRule(array $criteria): ?VehicleInsuranceBaseRule
    {
        $cacheKey = "insurance:base_rule:" . md5(json_encode($criteria));

        return Cache::remember($cacheKey, 21600, function () use ($criteria) {
            return VehicleInsuranceBaseRule::findBestMatch($criteria);
        });
    }

    public function calculateBaseInsurancePremium(VehicleInsuranceBaseRule $rule, float $invoiceValue, int $yearOption = 1): array
    {
        $odPremium = round($invoiceValue * $rule->od_factor, 2);
        $odAfterDiscount = $odPremium - round($odPremium * $rule->od_discount_rate, 2);
        $imt23 = round($odAfterDiscount * $rule->imt_23_rate, 2);
        $tpPremium = $rule->tp_basic;

        if ($yearOption === 3) {
            $tpPremium = $rule->tp_basic * 1.8;
        }

        return [
            'od_premium'         => $odPremium,
            'od_after_discount'  => $odAfterDiscount,
            'imt_23'             => $imt23,
            'tp_premium'         => $tpPremium,
            'base_premium'       => round($odAfterDiscount + $imt23 + $tpPremium, 2),
        ];
    }

    public function getInsuranceAddons(string $company, string $permit): array
    {
        $cacheKey = "insurance:addons:{$company}:{$permit}";

        return Cache::remember($cacheKey, 21600, function () use ($company, $permit) {
            return VehicleInsuranceAddonRate::getActiveAddons($company, $permit);
        });
    }

    // ==================== RTO ====================

    public function getRtoRule(array $criteria): ?VehicleRtoRule
    {
        $cacheKey = "rto:rule:" . md5(json_encode($criteria));

        return Cache::remember($cacheKey, 21600, function () use ($criteria) {
            return VehicleRtoRule::findBestMatch($criteria);
        });
    }

    public function calculateRto(VehicleRtoRule $rule, float $exShowroomPrice): array
    {
        $taxAmount = round($exShowroomPrice * $rule->tax_factor, 2);
        $surcharge = $rule->surcharge > 0 ? $rule->surcharge : round($taxAmount * 0.125, 2);

        return [
            'tax_amount'       => $taxAmount,
            'surcharge'        => $surcharge,
            'hypothecation'    => $rule->hypothecation,
            'green_tax'        => $rule->green_tax,
            'registration_fee' => $rule->registration_fee,
            'total_rto'        => round($taxAmount + $surcharge + $rule->hypothecation + $rule->green_tax + $rule->registration_fee, 2),
        ];
    }

    // ==================== ADDONS (RSA, Shield, Dealer Charges) ====================

    public function getDefaultRsa(string $modelCode, ?string $variantCode = null): ?array
    {
        $cacheKey = "addon:rsa:default:{$modelCode}:" . ($variantCode ?? 'all');

        return Cache::remember($cacheKey, 21600, function () use ($modelCode, $variantCode) {
            $rsa = VehicleAddon::getDefaultRsa($modelCode, $variantCode);
            return $rsa ? $rsa->toArray() : null;
        });
    }

    public function getAvailableRsaOptions(string $modelCode, ?string $variantCode = null): array
    {
        $cacheKey = "addon:rsa:options:{$modelCode}:" . ($variantCode ?? 'all');

        return Cache::remember($cacheKey, 21600, function () use ($modelCode, $variantCode) {
            return VehicleAddon::query()
                ->active()
                ->forVehicle($modelCode, $variantCode)
                ->ofType('rsa')
                ->orderBy('tenure_years')
                ->get(['id', 'tenure_years', 'name', 'amount', 'oem_share', 'dealer_share'])
                ->toArray();
        });
    }

    public function getDefaultShield(string $modelCode, ?string $variantCode = null): ?array
    {
        $cacheKey = "addon:shield:default:{$modelCode}:" . ($variantCode ?? 'all');

        return Cache::remember($cacheKey, 21600, function () use ($modelCode, $variantCode) {
            $shield = VehicleAddon::getDefaultShield($modelCode, $variantCode);
            return $shield ? $shield->toArray() : null;
        });
    }

    public function getAvailableShieldOptions(string $modelCode, ?string $variantCode = null): array
    {
        $cacheKey = "addon:shield:options:{$modelCode}:" . ($variantCode ?? 'all');

        return Cache::remember($cacheKey, 21600, function () use ($modelCode, $variantCode) {
            return VehicleAddon::query()
                ->active()
                ->forVehicle($modelCode, $variantCode)
                ->ofType('shield')
                ->orderBy('amount')
                ->get(['id', 'scheme_name', 'name', 'amount', 'oem_share', 'dealer_share'])
                ->toArray();
        });
    }

    public function getDealerCharges(string $segment, ?string $modelCode = null): array
    {
        $cacheKey = "addon:dealer_charges:{$segment}:" . ($modelCode ?? 'default');

        return Cache::remember($cacheKey, 21600, function () use ($segment, $modelCode) {
            $query = VehicleAddon::query()->active()->ofType('dealer_charges');

            if ($modelCode) {
                $query->forVehicle($modelCode);
            } else {
                $query->whereNull('model_code');
            }

            return $query->get(['name', 'amount', 'oem_share', 'dealer_share'])->keyBy('name')->toArray();
        });
    }

    // ==================== DISCOUNTS ====================

    public function getApplicableDiscounts(string $modelCode, ?string $variantCode = null, array $context = []): array
    {
        $cacheKey = "discounts:{$modelCode}:" . ($variantCode ?? 'all') . ':' . md5(json_encode($context));

        return Cache::remember($cacheKey, 21600, function () use ($modelCode, $variantCode, $context) {
            $query = VehicleDiscount::query()->active()->forVehicle($modelCode, $variantCode);

            if (!empty($context['only_new_vin'])) {
                $query->whereJsonContains('conditions->only_for_new_vin', true);
            }

            return $query->get()->keyBy('id')->toArray();
        });
    }

    public function calculateDiscount(VehicleDiscount $discount, array $context): array
    {
        $baseAmount = $context['invoice_value'] ?? $context['ex_showroom'] ?? 0;
        $amount = $discount->total_discount;

        if (($discount->conditions['calculation_type'] ?? '') === 'percentage') {
            $amount = round($baseAmount * ($discount->total_discount / 100), 2);
        }

        return [
            'discount_id'       => $discount->id,
            'discount_type'     => $discount->discount_type,
            'discount_category' => $discount->discount_category,
            'oem_share'         => round($amount * 0.6, 2),
            'dealer_share'      => round($amount * 0.4, 2),
            'total'             => round($amount, 2),
            'is_conditional'    => $discount->is_conditional,
            'linked_to'         => $discount->linked_to,
        ];
    }

    public function getWithheldDiscounts(array $allDiscounts, array $selectedOptions): array
    {
        return collect($allDiscounts)->filter(function ($discount) use ($selectedOptions) {
            if ($discount['linked_to'] === 'rsa' && empty($selectedOptions['rsa'])) return true;
            if ($discount['linked_to'] === 'shield' && empty($selectedOptions['shield'])) return true;

            if ($discount['linked_to'] === 'accessories') {
                $value = $selectedOptions['accessories_value'] ?? 0;
                $threshold = $discount['conditions']['min_accessories_value'] ?? 0;
                return $value < $threshold;
            }
            return false;
        })->values()->toArray();
    }

    public function applyDiscountBifurcation(array $discounts): array
    {
        $result = ['cash' => 0, 'credit_note' => 0, 'details' => []];

        foreach ($discounts as $d) {
            $mode = in_array($d['discount_type'], ['corporate', 'exchange', 'loyalty']) ? 'credit_note' : 'cash';
            $result[$mode] += $d['total'];
            $result['details'][] = ['type' => $d['discount_type'], 'mode' => $mode, 'amount' => $d['total']];
        }

        return $result;
    }
}