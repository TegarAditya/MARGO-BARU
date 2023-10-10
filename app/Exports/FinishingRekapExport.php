<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class FinishingRekapExport implements FromCollection, ShouldAutoSize
{
    use Exportable;

    private Collection $rekap;

    public function __construct(Collection $rekap)
    {
        $this->rekap = $rekap;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $rows = collect([]);

        $rows->push([
            'no' => 'No.',
            'vendor' => 'Vendor',
            'date' => 'Tanggal',
            'jenjang' => 'Jenjang',
            'no_spk' => 'No SPK',
            'oplagh' => 'Total Oplagh',
            'ongkos' => 'Total Ongkos Cetak',
        ]);

        $i = 0;

        foreach($this->rekap as $item) {
            $i++;

            $row = [
                'no' => $i,
                'vendor' => $item->vendor->name,
                'date' => $item->date,
                'jenjang' => $item->jenjang->name,
                'no_spk' => $item->no_spk,
                'oplagh' => (string) $item->estimasi_oplah,
                'ongkos' => (string) $item->total_cost,
            ];

            $rows->push($row);
        }

        return $rows;
    }
}
