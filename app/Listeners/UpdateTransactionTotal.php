<?php

namespace App\Listeners;

use App\Events\TransactionUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Transaction;
use App\Models\TransactionTotal;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\ReturnGood;
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

        $totalRecord = TransactionTotal::where('salesperson_id', $salesperson)->where('semester_id', $semester)->first();

        // If a total record exists, update it; otherwise, create a new record
        if ($totalRecord) {
            $totalRecord->update([
                'total_invoice' => $sum_amount['faktur'] ?? 0,
                'total_diskon' => $sum_amount['diskon'] ?? 0,
                'total_adjustment' => $sum_amount['adjustment'] ?? 0,
                'total_retur' => $sum_amount['retur'] ?? 0,
                'total_bayar' => $sum_amount['bayar'] ?? 0,
                'total_potongan' => $sum_amount['potongan'] ?? 0,
            ]);
        } else {
            TransactionTotal::create([
                'salesperson_id' => $salesperson,
                'total_invoice' => $sum_amount['faktur'] ?? 0,
                'total_diskon' => $sum_amount['diskon'] ?? 0,
                'total_adjustment' => $sum_amount['adjustment'] ?? 0,
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
        $payment = Payment::selectRaw('COALESCE(SUM(paid), 0) as bayar, COALESCE(SUM(discount), 0) as potongan')->where('salesperson_id', $salesperson)->where('semester_bayar_id', $semester)->first();

        $faktur = $bill_amount['faktur'] ?? 0;
        $diskon = $bill_amount['diskon'] ?? 0;
        $adjustment = $bill_amount['adjustment'] ?? 0;
        $retur = $bill_amount['retur'] ?? 0;
        $bayar = $payment->bayar;
        $potongan = $payment->potongan;

        $pembayaran = ($bill_amount['bayar'] ?? 0) + ($bill_amount['potongan'] ?? 0);

        if ($bill) {
            $saldo_awal = $bill->previous ? $bill->previous->saldo_akhir : 0;
            $bill->update([
                'saldo_awal' => $saldo_awal,
                'jual' => $faktur,
                'diskon' => $diskon,
                'adjustment' => $adjustment,
                'retur' => $retur,
                'bayar' => $bayar,
                'potongan' => $potongan,
                'saldo_akhir' => ($saldo_awal + $faktur) - ($adjustment + $diskon + $retur + $bayar + $potongan),
                'tagihan' => $faktur - ($diskon + $retur),
                'pembayaran' => $pembayaran,
                'piutang' => ($saldo_awal + $faktur) - ($adjustment + $diskon + $retur + $pembayaran)
            ]);
        } else {
            $previous = Bill::where('salesperson_id', $salesperson)->where('semester_id', prevSemester($semester))->first();

            $saldo_awal = $previous ? $previous->saldo_akhir : 0;
            Bill::create([
                'semester_id' => $semester,
                'salesperson_id' => $salesperson,
                'previous_id' => $previous ? $previous->id : null,
                'saldo_awal' => $saldo_awal,
                'jual' => $faktur,
                'diskon' => $diskon,
                'adjustment' => $adjustment,
                'retur' => $retur,
                'bayar' => $bayar,
                'potongan' => $potongan,
                'saldo_akhir' => ($saldo_awal + $faktur) - ($adjustment + $diskon + $retur + $bayar + $potongan),
                'tagihan' => $faktur - ($diskon + $retur),
                'pembayaran' => $pembayaran,
                'piutang' => ($saldo_awal + $faktur) - ($adjustment + $diskon + $retur + $pembayaran)
            ]);
        }
    }
}
