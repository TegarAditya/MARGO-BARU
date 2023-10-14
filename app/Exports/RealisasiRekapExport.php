<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RealisasiRekapExport implements FromCollection, ShouldAutoSize
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
            'code' => 'Kode',
            'buku' => 'Buku',
            'isi_cover' => 'Isi - Cover',
            'jenjang' => 'Jenjang',
            'kurikulum' => 'Kurikulum',
            'mapel' => 'Mapel',
            'kelas' => 'Kls',
            'halaman' => 'Hal',
            'spk' => 'SPK',
            'realisasi' => 'Realisasi'
        ]);

        $i = 0;

        foreach($this->rekap as $item) {
            $i++;

            $row = [
                'no' => $i,
                'code' => $item->code,
                'buku' => $item->short_name,
                'isi_cover' => ($item->isi ? $item->isi->name : '') .''. ($item->isi && $item->cover ? ' - ' : ' ') .''. ($item->cover ? $item->cover->name : ''),
                'jenjang' => $item->jenjang->name,
                'kurikulum' => $item->kurikulum->name,
                'mapel' => $item->mapel->name,
                'kelas' => $item->kelas->code,
                'halaman' => $item->halaman->code,
                'spk' => (string) $item->estimasi,
                'realisasi' => (string) $item->quantity,
            ];

            $rows->push($row);
        }

        return $rows;
    }
}
