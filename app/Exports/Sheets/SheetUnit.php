<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use App\Models\Unit;

class SheetUnit implements FromQuery, WithTitle
{
    public function query()
    {
        return Unit::query()->select('code', 'name');
    }

    public function title(): string
    {
        return 'Unit';
    }
}
