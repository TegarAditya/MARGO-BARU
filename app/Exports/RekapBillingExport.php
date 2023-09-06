<?php

namespace App\Exports;

use App\Models\Bill;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\Exportable;

class RekapBillingExport implements FromCollection, ShouldAutoSize
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $semester = setting('current_semester');
        $rekap_billings = Bill::with(['semester', 'salesperson'])->where('semester_id', $semester)->get();
        $rows = collect([]);

        $rows->push([
            'no' => 'No.',
            'salesperson_code' => 'Kode Sales',
            'salesperson_name' => 'Nama Sales',
            'saldo_awal' => 'Saldo Awal',
            'penjualan' => 'Penjualan',
            'diskon' => 'Diskon',
            'adjustment' => 'Adjustment',
            'retur' => 'Retur',
            'pembayaran' => 'Pembayaran',
            'potongan' => 'Potongan',
            'saldo_akhir' => 'Saldo Akhir',
        ]);

        $i = 0;
        foreach ($rekap_billings as $bill) {
            $i++;

            $salesperson = $bill->salesperson;

            $row = [
                'no' => $i,
                'salesperson_code' => $salesperson->code,
                'salesperson_name' => $salesperson->short_name,
                'saldo_awal' => $bill->saldo_awal,
                'penjualan' => $bill->penjualan,
                'diskon' => $bill->diskon,
                'adjustment' => $bill->adjustment,
                'retur' => $bill->retur,
                'pembayaran' => $bill->bayar,
                'potongan' => $bill->potongan,
                'saldo_akhir' => $bill->saldo_akhir,
            ];

            $rows->push($row);
        }

        return $rows;
    }
}
