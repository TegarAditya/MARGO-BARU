<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CetakRekapExport implements FromCollection, ShouldAutoSize
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
        //tgl, jenjang, no spk, oplagh, total ongkos cetak, isi/cover

        $rows->push([
            'no' => 'No.',
            'date' => 'Tanggal',
            'vendor' => 'Vendor',
            'jenjang' => 'Jenjang',
            'no_spc' => 'No SPK',
            'oplagh' => 'Total Oplagh',
            'ongkos' => 'Total Ongkos Cetak',
        ]);

        $i = 0;

        foreach($this->rekap as $item) {
            $i++;

            $row = [
                'no' => $i,
                'date' => $item->date,
                'vendor' => $item->vendor->name,
                'jenjang' => $item->jenjang->name,
                'no_spc' => $item->no_spc,
                'oplagh' => (string) $item->estimasi_oplah,
                'ongkos' => (string) $item->total_cost,
            ];

            $rows->push($row);
        }

        return $rows;
    }
}
