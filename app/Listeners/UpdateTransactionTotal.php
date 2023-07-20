<?php

namespace App\Listeners;

use App\Events\TransactionUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Transaction;
use App\Models\TransactionTotal;
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
    }
}
