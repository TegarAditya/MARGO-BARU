<?php

namespace App\Exports;

use App\Models\Bill;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class BillingExport implements FromCollection, ShouldAutoSize
{
    use Exportable;

    private Collection $saldo_awal;
    private Collection $sales;

    public function __construct(Collection $saldo_awal, Collection $sales)
    {
        $this->saldo_awal = $saldo_awal;
        $this->sales = $sales;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $rows = collect([]);

        $rows->push([
            'no' => 'No.',
            'salesperson_code' => 'Kode Sales',
            'salesperson_name' => 'Nama Sales',
            'saldo_awal' => 'Saldo Awal',
            'penjualan' => 'Penjualan',
            'diskon' => 'Diskon',
            'retur' => 'Retur',
            'pembayaran' => 'Pembayaran',
            'potongan' => 'Potongan',
            'saldo_akhir' => 'Saldo Akhir',
        ]);

        $i = 0;

        foreach($this->sales as $item) {
            $i++;

            $awal = $this->saldo_awal->where('id', $item->id)->first();
            $pertama = $awal->pengambilan - ($awal->diskon + $awal->retur + $awal->bayar + $awal->potongan);
            $terakhir = $pertama + ($item->pengambilan - ($item->diskon + $item->retur + $item->bayar + $item->potongan));

            $row = [
                'no' => $i,
                'salesperson_code' => $item->code,
                'salesperson_name' => $item->short_name,
                'saldo_awal' => (string) $pertama,
                'penjualan' => (string) $item->pengambilan,
                'diskon' => (string) $item->diskon,
                'retur' => (string) $item->retur,
                'pembayaran' => (string) $item->bayar,
                'potongan' => (string) $item->potongan,
                'saldo_akhir' => (string) $terakhir,
            ];

            $rows->push($row);
        }

        return $rows;
    }
}
