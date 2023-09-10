<?php

namespace App\Exports;

use App\Models\Bill;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StockExport implements FromCollection, ShouldAutoSize
{
    use Exportable;

    private Collection $saldo_awal;
    private Collection $saldo_akhir;

    public function __construct(Collection $saldo_awal, Collection $saldo_akhir)
    {
        $this->saldo_awal = $saldo_awal;
        $this->saldo_akhir = $saldo_akhir;
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
            'qty_awal' => 'Quantity Awal',
            'in' => 'Masuk',
            'adjustment' => 'Adjustment',
            'out' => 'Keluar',
            'qty_akhir' => 'Quantity Akhir',
        ]);

        $i = 0;

        foreach($this->saldo_akhir as $item) {
            $i++;

            $awal = $this->saldo_awal->where('id', $item->id)->first();
            $pertama = $awal->in + $awal->out;

            $terakhir = $pertama + ($item->in + $item->adjustment + $item->out);

            $row = [
                'no' => $i,
                'type' => $item->book_type,
                'isi_cover' => ($item->isi ? $item->isi->name : '') .''. ($item->isi && $item->cover ? ' - ' : ' ') .''. ($item->cover ? $item->cover->name : ''),
                'buku' => $item->short_name,
                'qty_awal' => (string) $pertama,
                'in' => (string) $item->in,
                'adjustment' => (string) $item->adjustment,
                'out' => (string) $item->out,
                'qty_akhir' => (string) $terakhir,
            ];

            $rows->push($row);
        }

        return $rows;
    }
}
