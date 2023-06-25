<?php
namespace App\Services;

use Carbon\Carbon;
use DB;
use App\Models\SalesOrder;
use App\Models\BookVariant;
use App\Models\StockMovement;

class StockService
{
    public static function createMovement($type_movement, $transaction_type, $reference, $date,  $product, $quantity)
    {
        $estimation = StockMovement::create([
            'warehouse' => 1,
            'movement_date' => Carbon::now()->format('d-m-Y'),
            'movement_type' => $type_movement,
            'transaction_type' => $transaction_type,
            'reference_id' => $reference,
            'reference_date' => $date,
            'product_id' => $product,
            'quantity' => $quantity,
        ]);
    }

    public static function updateStock($product_id, $quantity) {
        $product = BookVariant::where('id', $product_id)->update([
            'stock' => DB::raw("stock + $quantity")
        ]);
    }

    public static function editMovement($type_movement, $transaction_type, $reference, $date, $product, $quantity)
    {
        $reversal = StockMovement::where('transaction_type', $transaction_type)->where('reference_id', $reference)
                    ->where('product_id', $product)->orderBy('id', 'DESC')->first();

        StockMovement::create([
            'warehouse' => 1,
            'movement_date' => Carbon::now()->format('d-m-Y'),
            'movement_type' => 'revisi',
            'transaction_type' => $transaction_type,
            'reference_id' => $reference,
            'reference_date' => $date,
            'product_id' => $product,
            'quantity' => -1 * $reversal->quantity,
            'reversal_of_id' => $reversal->id
        ]);

        $stock = StockMovement::create([
            'warehouse' => 1,
            'movement_date' => Carbon::now()->format('d-m-Y'),
            'movement_type' => $type_movement,
            'transaction_type' => $transaction_type,
            'reference_id' => $reference,
            'reference_date' => $date,
            'product_id' => $product,
            'quantity' => $quantity,
        ]);
    }
}
