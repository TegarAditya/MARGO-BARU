<?php

namespace App\Listeners;

use App\Events\TransactionUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Transaction;
use App\Models\TransactionTotal;
use App\Models\Bill;
use DB;

class UpdateTransactionTotal
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TransactionUpdated $event): void
    {
        $transaction = $event->transaction;
        $salesperson = $transaction->salesperson_id;
        $semester = $transaction->semester_id;

        $sum_amount = Transaction::where('salesperson_id', $salesperson)
                    ->select('type', DB::raw('SUM(amount) as total_amount'))
                    ->groupBy('type')
                    ->get()
                    ->pluck('total_amount', 'type');

        $totalRecord = TransactionTotal::where('salesperson_id', $salesperson)->first();

        // If a total record exists, update it; otherwise, create a new record
        if ($totalRecord) {
            $totalRecord->update([
                'total_invoice' => $sum_amount['faktur'] ?? 0,
                'total_diskon' => $sum_amount['diskon'] ?? 0,
                'total_retur' => $sum_amount['retur'] ?? 0,
                'total_bayar' => $sum_amount['bayar'] ?? 0,
                'total_potongan' => $sum_amount['potongan'] ?? 0,
            ]);
        } else {
            TransactionTotal::create([
                'salesperson_id' => $salesperson,
                'total_invoice' => $sum_amount['faktur'] ?? 0,
                'total_diskon' => $sum_amount['diskon'] ?? 0,
                'total_retur' => $sum_amount['retur'] ?? 0,
                'total_bayar' => $sum_amount['bayar'] ?? 0,
                'total_potongan' => $sum_amount['potongan'] ?? 0,
            ]);
        }


        $bill_amount = Transaction::where('salesperson_id', $salesperson)
                    ->where('semester_id', $semester)
                    ->select('type', DB::raw('SUM(amount) as total_amount'))
                    ->groupBy('type')
                    ->get()
                    ->pluck('total_amount', 'type');

        $bill = Bill::where('salesperson_id', $salesperson)->where('semester_id', $semester)->first();

        $faktur = $bill_amount['faktur'] ?? 0;
        $diskon = $bill_amount['diskon'] ?? 0;
        $retur = $bill_amount['retur'] ?? 0;
        $bayar = $bill_amount['bayar'] ?? 0;
        $potongan = $bill_amount['potongan'] ?? 0;

        if ($bill) {
            $bill->update([
                'saldo_awal' => $bill->previous ? $bill->previous->saldo_akhir : 0,
                'jual' => $faktur,
                'diskon' => $diskon,
                'retur' => $retur,
                'bayar' => $bayar,
                'potongan' => $potongan,
                'saldo_akhir' => $faktur - ($diskon + $retur + $bayar + $potongan),
            ]);
        } else {
            $previous = Bill::where('salesperson_id', $salesperson)->where('semester_id', prevSemester($semester))->first();

            Bill::create([
                'semester_id' => $semester,
                'salesperson_id' => $salesperson,
                'previous_id' => $previous ? $previous->id : null,
                'saldo_awal' => $previous ? $previous->saldo_akhir : 0,
                'jual' => $faktur,
                'diskon' => $diskon,
                'retur' => $retur,
                'bayar' => $bayar,
                'potongan' => $potongan,
                'saldo_akhir' => $faktur - ($diskon + $retur + $bayar + $potongan),
            ]);
        }
    }
}
