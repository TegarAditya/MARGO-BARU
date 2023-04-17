<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Models\Material;
use App\Models\Unit;

class MaterialImport implements ToCollection, WithHeadingRow, WithMultipleSheets
{
    /**
     * @return array
     */
    public function sheets(): array
    {
        return [
            0 => $this,
        ];
    }

    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $unit = Unit::where('code', $row['unit'])->first()->id;

            Material::create([
                'code' => $row['code'],
                'name' => $row['name'],
                'category' => $row['category'],
                'unit_id' => $unit ?? null,
                'cost' => $row['cost'],
                'stock' => $row['stock'],
                'warehouse_id' => 1,
            ]);
        }
    }
}
