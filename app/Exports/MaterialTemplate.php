<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;

use App\Exports\Sheets\SheetUnit;
use App\Exports\Sheets\SheetMaterialCategory;
use App\Exports\Sheets\SheetTemplateMaterial;

class MaterialTemplate implements WithMultipleSheets, FromCollection, WithTitle
{
    use Exportable;

    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = $this;
        $sheets[] = new SheetMaterialCategory();
        $sheets[] = new SheetUnit();
        return $sheets;
    }

    public function collection()
    {
        $rows = collect([]);

        $rows->push([
            'code' => 'CODE',
            'name' => 'NAME',
            'category' => 'CATEGORY',
            'unit' => 'UNIT',
            'cost' => 'COST',
            'stock' => 'STOCK',
        ]);

        return $rows;
    }

    public function title(): string
    {
        return 'Template Material';
    }
}
