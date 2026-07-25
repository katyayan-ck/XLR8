<?php

namespace App\Imports\Vehicle;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class VehicleMasterImport implements ToCollection, WithHeadingRow
{
    protected string $wefDate;

    public function __construct(string $wefDate)
    {
        $this->wefDate = $wefDate;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // TODO: Implement logic to create/update vehicle_model and vehicle_variant
            // Use model_code as unique key
            // Update status, CSD code, color_name, etc.
        }
    }
}