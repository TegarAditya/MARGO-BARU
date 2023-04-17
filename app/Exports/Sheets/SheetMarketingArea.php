<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use App\Models\MarketingArea;

class SheetMarketingArea implements FromQuery, WithTitle
{
    public function query()
    {
        return MarketingArea::query()->select('name');
    }

    public function title(): string
    {
        return 'Marketing Area';
    }
}
