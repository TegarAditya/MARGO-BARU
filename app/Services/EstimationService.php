<?php
namespace App\Services;

use App\Models\EstimationMovement;
use App\Models\ProductionEstimation;
use App\Models\SalesOrder;
use Carbon\Carbon;
use DB;

class EstimationService
{
    public static function createMovement($type_movement, $reference_type, $reference, $product, $quantity, $type)
    {
        $estimation = EstimationMovement::updateOrCreate([
            'movement_type' => $type_movement,
            'reference_type' => $reference_type,
            'reference_id' => $reference,
            'product_id' => $product,
            'type' => $type,
        ], [
            'movement_date' => Carbon::now()->format('d-m-Y'),
            'quantity' => DB::raw("quantity + $quantity")
        ]);
    }

    public static function createProduction($product, $quantity, $type)
    {
        $production = ProductionEstimation::where('product_id', $product)->where('type', $type)->first();

        if ($production) {
            $production->estimasi += $quantity;
            $production->save();
        } else {
            $new = ProductionEstimation::create([
                'product_id' => $product,
                'type' => $type,
                'estimasi' => $quantity,
            ]);
        }
    }

    public static function editMovement($type_movement, $reference_type, $reference, $product, $quantity, $type)
    {
        $estimation = EstimationMovement::where('movement_type', $type_movement)
                    ->where('reference_type', $reference_type)
                    ->where('reference_id', $reference)
                    ->where('product_id', $product)
                    ->where('type', $type)
                    ->first();

        $estimation->movement_date = Carbon::now()->format('d-m-Y');
        $estimation->quantity = $quantity;
        $estimation->save();
    }
    //Edit movement menggunakan quantity baru
    //Edit production menggunakan quantity selisih
    public static function editProduction($product, $quantity, $type)
    {
        $production = ProductionEstimation::where('product_id', $product)->where('type', $type)->first();

        if ($production) {
            $production->estimasi += $quantity;
            $production->save();
        }
    }

    public static function updateMoved($order, $quantity) {
        $order = SalesOrder::find($order);
        $order->moved += $quantity;
        $order->save();
    }

    public static function updateRetur($order, $quantity) {
        $order = SalesOrder::find($order);
        $order->retur += $quantity;
        $order->save();
    }
}
