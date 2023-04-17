<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;

class SheetMaterialCategory implements FromCollection, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $rows = collect([]);

        $rows->push(['paper']);
        $rows->push(['plate']);

        return $rows;
    }

    public function title(): string
    {
        return 'Category';
    }
}
