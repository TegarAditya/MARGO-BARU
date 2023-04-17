<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;

use App\Exports\Sheets\SheetMarketingArea;
use App\Exports\Sheets\SheetTemplateSalesperson;

class SalespersonTemplate implements WithMultipleSheets, FromCollection, WithTitle
{
    use Exportable;

    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = $this;
        $sheets[] = new SheetMarketingArea();
        return $sheets;
    }

    public function collection()
    {
        $rows = collect([]);

        $rows->push([
            'code' => 'CODE',
            'name' => 'NAME',
            'area_pemasaran' => 'AREA_PEMASARAN',
            'phone' => 'PHONE',
            'company' => 'COMPANY',
            'address' => 'ADDRESS',
        ]);

        return $rows;
    }

    public function title(): string
    {
        return 'Template Salesperson';
    }
}
