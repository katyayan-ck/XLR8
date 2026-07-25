<?php

namespace App\Services\Vehicle;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\Vehicle\VehicleMasterImport;
use App\Imports\Vehicle\PricingImport;
use App\Imports\Vehicle\RsaImport;
use App\Imports\Vehicle\ShieldImport;

class VehiclePricingImportService
{
    protected array $importLog = [];
    protected string $wefDate;
    protected bool $dryRun = false;

    /**
     * Main entry point for importing the pricing workbook
     */
    public function import(UploadedFile $file, string $wefDate, bool $dryRun = false): array
    {
        $this->wefDate = $wefDate;
        $this->dryRun = $dryRun;
        $this->importLog = [];

        try {
            $this->log('info', "Starting Vehicle Pricing Import | WEF: {$wefDate} | DryRun: " . ($dryRun ? 'Yes' : 'No'));

            // Step 1: Vehicle Master Data (Models + Variants)
            $this->importVehicleMaster($file);

            // Step 2: Pricing Data
            $this->importPricing($file);

            // Step 3: Addons (RSA + Shield)
            $this->importAddons($file);

            // Step 4: Discounts (Corporate, Exchange, etc.)
            // $this->importDiscounts($file); // Can be added later

            $this->log('success', 'Import process completed successfully.');

            return [
                'success' => true,
                'dry_run' => $dryRun,
                'log'     => $this->importLog,
            ];

        } catch (\Exception $e) {
            $this->log('error', 'Import failed: ' . $e->getMessage());
            Log::error('Vehicle Pricing Import Error', ['exception' => $e]);

            return [
                'success' => false,
                'error'   => $e->getMessage(),
                'log'     => $this->importLog,
            ];
        }
    }

    /**
     * Import Vehicle Master (Models & Variants)
     */
    protected function importVehicleMaster(UploadedFile $file): void
    {
        $this->log('info', 'Importing Vehicle Master data...');

        if ($this->dryRun) {
            $this->log('info', '[Dry Run] Skipping actual import of Vehicle Master');
            return;
        }

        Excel::import(new VehicleMasterImport($this->wefDate), $file);
        $this->log('success', 'Vehicle Master import completed.');
    }

    /**
     * Import Pricing Data
     */
    protected function importPricing(UploadedFile $file): void
    {
        $this->log('info', 'Importing Pricing data...');

        if ($this->dryRun) {
            $this->log('info', '[Dry Run] Skipping actual import of Pricing');
            return;
        }

        Excel::import(new PricingImport($this->wefDate), $file);
        $this->log('success', 'Pricing import completed.');
    }

    /**
     * Import Addons (RSA + Shield)
     */
    protected function importAddons(UploadedFile $file): void
    {
        $this->log('info', 'Importing RSA & Shield data...');

        if ($this->dryRun) {
            $this->log('info', '[Dry Run] Skipping actual import of Addons');
            return;
        }

        // You can create separate imports or one combined import
        Excel::import(new RsaImport($this->wefDate), $file);
        Excel::import(new ShieldImport($this->wefDate), $file);

        $this->log('success', 'Addons (RSA + Shield) import completed.');
    }

    /**
     * Logging helper
     */
    protected function log(string $type, string $message): void
    {
        $this->importLog[] = [
            'type'      => $type,
            'message'   => $message,
            'timestamp' => now()->toDateTimeString(),
        ];
    }

    public function getLog(): array
    {
        return $this->importLog;
    }
}