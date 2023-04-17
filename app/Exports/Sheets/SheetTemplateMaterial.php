<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;

class SheetTemplateMaterial implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
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
