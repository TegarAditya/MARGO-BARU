<?php
namespace App\Services;

use Carbon\Carbon;
use DB;
use App\Models\Transaction;
use App\Models\ProductionTransaction;
use App\Models\Salesperson;
use App\Events\TransactionUpdated;
use App\Events\ProductionTransactionUpdated;

class TransactionService
{
    public static function createTransaction($date, $description, $salesperson, $semester,
        $type, $reference, $reference_no, $amount, $category)
    {
        $transaction = Transaction::create([
            'date' => Carbon::now()->format('d-m-Y'),
            'description' => $description,
            'salesperson_id' => $salesperson,
            'semester_id' => $semester,
            'type' => $type,
            'reference_id' => $reference,
            'reference_no' => $reference_no,
            'transaction_date' => $date,
            'amount' => $amount,
            'category' => $category,
            'status' => 0,
        ]);

        event(new TransactionUpdated($transaction));
    }

    public static function editTransaction($date, $description, $salesperson, $semester,
        $type, $reference, $reference_no, $amount, $category)
    {
        $reversal = Transaction::where('type', $type)->where('reference_id', $reference)->where('semester_id', $semester)
                    ->where('salesperson_id', $salesperson)->orderBy('id', 'DESC')->first();

        Transaction::create([
            'date' => Carbon::now()->format('d-m-Y'),
            'description' => $description,
            'salesperson_id' => $salesperson,
            'semester_id' => $semester,
            'type' => $type,
            'reference_id' => $reference,
            'reference_no' => $reference_no,
            'transaction_date' => $date,
            'amount' => -1 * $reversal->amount,
            'category' => $category,
            'status' => 0,
            'reversal_of_id' => $reversal->id
        ]);

        $transaction = Transaction::create([
            'date' => Carbon::now()->format('d-m-Y'),
            'description' => $description,
            'salesperson_id' => $salesperson,
            'semester_id' => $semester,
            'type' => $type,
            'reference_id' => $reference,
            'reference_no' => $reference_no,
            'transaction_date' => $date,
            'amount' => $amount,
            'category' => $category,
            'status' => 0,
        ]);

        event(new TransactionUpdated($transaction));
    }

    public static function createProductionTransaction($date, $description, $vendor, $semester,
        $type, $reference, $reference_no, $amount, $category)
    {
        $transaction = ProductionTransaction::create([
            'date' => Carbon::now()->format('d-m-Y'),
            'description' => $description,
            'vendor_id' => $vendor,
            'semester_id' => $semester,
            'type' => $type,
            'reference_id' => $reference,
            'reference_no' => $reference_no,
            'transaction_date' => $date,
            'amount' => $amount,
            'category' => $category,
            'status' => 0,
        ]);

        event(new ProductionTransactionUpdated($transaction));
    }

    public static function editProductionTransaction($date, $description, $vendor, $semester,
        $type, $reference, $reference_no, $amount, $category)
    {
        $reversal = ProductionTransaction::where('type', $type)->where('reference_id', $reference)->where('semester_id', $semester)
                    ->where('vendor_id', $vendor)->orderBy('id', 'DESC')->first();

        ProductionTransaction::create([
            'date' => Carbon::now()->format('d-m-Y'),
            'description' => $description,
            'vendor_id' => $vendor,
            'semester_id' => $semester,
            'type' => $type,
            'reference_id' => $reference,
            'reference_no' => $reference_no,
            'transaction_date' => $date,
            'amount' => -1 * $reversal->amount,
            'category' => $category,
            'status' => 0,
            'reversal_of_id' => $reversal->id
        ]);

        $transaction = ProductionTransaction::create([
            'date' => Carbon::now()->format('d-m-Y'),
            'description' => $description,
            'vendor_id' => $vendor,
            'semester_id' => $semester,
            'type' => $type,
            'reference_id' => $reference,
            'reference_no' => $reference_no,
            'transaction_date' => $date,
            'amount' => $amount,
            'category' => $category,
            'status' => 0,
        ]);

        event(new ProductionTransactionUpdated($transaction));
    }
}
