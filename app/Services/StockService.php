<?php
namespace App\Services;

use Carbon\Carbon;
use DB;
use App\Models\SalesOrder;
use App\Models\BookVariant;
use App\Models\StockMovement;
use App\Models\Material;

class StockService
{
    public static function createStockAwal($product, $quantity)
    {
        $awal = StockMovement::where('product_id', $product)->where('transaction_type', 'awal')->first();

        if (!$awal) {
            $estimation = StockMovement::create([
                'warehouse' => 1,
                'movement_date' => Carbon::now()->format('d-m-Y'),
                'movement_type' => 'in',
                'transaction_type' => 'awal',
                'reference_id' => $product,
                'reference_date' => Carbon::now()->format('d-m-Y'),
                'product_id' => $product,
                'quantity' => $quantity,
            ]);
        }
    }

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

    public static function createMovementMaterial($type_movement, $transaction_type, $reference, $date,  $product, $quantity)
    {
        $estimation = StockMovement::create([
            'warehouse' => 2,
            'movement_date' => Carbon::now()->format('d-m-Y'),
            'movement_type' => $type_movement,
            'transaction_type' => $transaction_type,
            'reference_id' => $reference,
            'reference_date' => $date,
            'material_id' => $product,
            'quantity' => $quantity,
        ]);
    }

    public static function updateStockMaterial($product_id, $quantity) {
        $product = Material::where('id', $product_id)->update([
            'stock' => DB::raw("stock + $quantity")
        ]);
    }

    public static function editMovementMaterial($type_movement, $transaction_type, $reference, $date, $product, $quantity)
    {
        $reversal = StockMovement::where('transaction_type', $transaction_type)->where('reference_id', $reference)
                    ->where('material_id', $product)->orderBy('id', 'DESC')->first();

        StockMovement::create([
            'warehouse' => 2,
            'movement_date' => Carbon::now()->format('d-m-Y'),
            'movement_type' => 'revisi',
            'transaction_type' => $transaction_type,
            'reference_id' => $reference,
            'reference_date' => $date,
            'material_id' => $product,
            'quantity' => -1 * $reversal->quantity,
            'reversal_of_id' => $reversal->id
        ]);

        $stock = StockMovement::create([
            'warehouse' => 2,
            'movement_date' => Carbon::now()->format('d-m-Y'),
            'movement_type' => $type_movement,
            'transaction_type' => $transaction_type,
            'reference_id' => $reference,
            'reference_date' => $date,
            'material_id' => $product,
            'quantity' => $quantity,
        ]);
    }

    public static function printPlate($reference, $date, $plate, $realisasi)
    {
        Material::where('id', $plate)->update([
            'stock' => DB::raw("stock - $realisasi")
        ]);

        StockMovement::create([
            'warehouse' => 2,
            'movement_date' => Carbon::now()->format('d-m-Y'),
            'movement_type' => 'out',
            'transaction_type' => 'plating',
            'reference_id' => $reference,
            'reference_date' => $date,
            'material_id' => $plate,
            'quantity' => $realisasi,
        ]);


        $gum = Material::where('code', 'GUM')->first();
        $gum_qty = 2.5 * $realisasi;

        StockMovement::create([
            'warehouse' => 2,
            'movement_date' => Carbon::now()->format('d-m-Y'),
            'movement_type' => 'out',
            'transaction_type' => 'plating',
            'reference_id' => $reference,
            'reference_date' => $date,
            'material_id' => $gum->id,
            'quantity' => $gum_qty,
        ]);

        $gum->update([
            'stock' => DB::raw("stock - $gum_qty")
        ]);


        $developer = Material::where('code', 'DEVELOPER')->first();
        $developer_qty = 15 * $realisasi;

        StockMovement::create([
            'warehouse' => 2,
            'movement_date' => Carbon::now()->format('d-m-Y'),
            'movement_type' => 'out',
            'transaction_type' => 'plating',
            'reference_id' => $reference,
            'reference_date' => $date,
            'material_id' => $developer->id,
            'quantity' => $developer_qty,
        ]);

        $developer->update([
            'stock' => DB::raw("stock - $developer_qty")
        ]);
    }
}
