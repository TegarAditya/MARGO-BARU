<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProduksiExport implements FromCollection, ShouldAutoSize
{
    use Exportable;
    private Collection $saldo;

    public function __construct(Collection $saldo)
    {
        $this->saldo = $saldo;
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
            'isi_cover' => 'Isi dan Cover',
            'buku' => 'Buku',
            'produksi' => 'Produksi',
            'terpakai' => 'Terpakai',
        ]);

        $i = 0;

        foreach($this->saldo as $item) {
            $i++;

            $row = [
                'no' => $i,
                'type' => $item->book_type,
                'isi_cover' => ($item->isi ? $item->isi->name : '') .''. ($item->isi && $item->cover ? ' - ' : ' ') .''. ($item->cover ? $item->cover->name : ''),
                'buku' => $item->short_name,
                'produksi' => (string) $item->in,
                'terpakai' => (string) $item->out,
            ];

            $rows->push($row);
        }

        return $rows;
    }
}
