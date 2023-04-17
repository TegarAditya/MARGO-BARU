<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use App\Models\Salesperson;
use App\Models\MarketingArea;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SalespersonImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $marketing_area = MarketingArea::firstOrCreate([
                'name' => $row['area_pemasaran']
            ]);

            $salesperson = Salesperson::create([
                'code' => $row['code'],
                'name' => $row['name'],
                'marketing_area_id' => $marketing_area->id,
                'phone' => $row['phone'],
                'company' => $row['company'],
                'address' => $row['address']
            ]);
        }
    }
}
