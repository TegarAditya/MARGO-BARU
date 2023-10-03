<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StockOpname implements FromCollection, ShouldAutoSize
{
    use Exportable;

    private Collection $stock_opname;

    public function __construct(Collection $stock_opname)
    {
        $this->stock_opname = $stock_opname;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $rows = collect([]);

        $rows->push([
            'no' => 'No.',
            'type' => 'Jenis',
            'buku' => 'Buku',
            'isi_cover' => 'Isi - Cover',
            'jenjang' => 'Jenjang',
            'kurikulum' => 'Kurikulum',
            'mapel' => 'Mapel',
            'kelas' => 'Kls',
            'halaman' => 'Hal',
            'Stock' => 'Stock',
        ]);

        $i = 0;

        foreach($this->stock_opname as $item) {
            $i++;

            $row = [
                'no' => $i,
                'type' => $item->book_type,
                'buku' => $item->short_name,
                'isi_cover' => ($item->isi ? $item->isi->name : '') .''. ($item->isi && $item->cover ? ' - ' : ' ') .''. ($item->cover ? $item->cover->name : ''),
                'jenjang' => $item->jenjang->name,
                'kurikulum' => $item->kurikulum->name,
                'mapel' => $item->mapel->name,
                'kelas' => $item->kelas->code,
                'halaman' => $item->halaman->code,
                'stock' => (string) $item->stock,
            ];

            $rows->push($row);
        }

        return $rows;
    }
}
