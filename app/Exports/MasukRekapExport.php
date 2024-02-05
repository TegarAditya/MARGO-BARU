<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\FinishingMasuk;

class MasukRekapExport implements FromCollection, ShouldAutoSize
{
    use Exportable;

    private Collection $rekap;

    public function __construct(Collection $rekap)
    {
        $this->rekap = $rekap;
    }

    public function collection()
    {
        $rows = collect([]);

        $rows->push([
            'no' => 'No.',
            'vendor' => 'Vendor',
            'no_spk' => 'No SPK Vendor',
            'date' => 'Tanggal SJ',
            'oplagh' => 'Oplagh Masuk',
        ]);

        $i = 0;

        foreach($this->rekap as $item) {
            $i++;

            $detail = FinishingMasuk::where('no_spk', $item->no_spk)->first();

            $row = [
                'no' => $i,
                'vendor' => $detail->vendor->name,
                'no_spk' => $item->no_spk,
                'date' => $detail->date,
                'oplagh' => (string) $item->quantity,
            ];

            $rows->push($row);
        }

        return $rows;
    }
}
