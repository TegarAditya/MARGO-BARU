<?php

namespace App\Listeners;

use App\Events\ProductionTransactionUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use DB;
use App\Models\ProductionTransaction;
use App\Models\ProductionTransactionTotal;

class UpdateProductionTransactionTotal
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
    public function handle(ProductionTransactionUpdated $event): void
    {
        $transaction = $event->transaction;
        $vendor = $transaction->vendor_id;
        $semester = $transaction->semester_id;

        $sum_amount = ProductionTransaction::where('vendor_id', $vendor)
                    ->select('type', DB::raw('SUM(amount) as total_amount'))
                    ->groupBy('type')
                    ->get()
                    ->pluck('total_amount', 'type');

        $totalRecord = ProductionTransactionTotal::where('vendor_id', $vendor)->first();

        $total_fee = ($sum_amount['cetak'] ?? 0) + ($sum_amount['finishing'] ?? 0);
        $total_payment = $sum_amount['bayar'] ?? 0;
        $outstanding_fee = $total_fee - $total_payment;
        // If a total record exists, update it; otherwise, create a new record
        if ($totalRecord) {
            $totalRecord->update([
                'total_fee' => $total_fee ,
                'total_payment' => $total_payment,
                'outstanding_fee' => $outstanding_fee,
            ]);
        } else {
            ProductionTransactionTotal::create([
                'vendor_id' => $vendor,
                'total_fee' => $total_fee ,
                'total_payment' => $total_payment,
                'outstanding_fee' => $outstanding_fee,
            ]);
        }
    }
}