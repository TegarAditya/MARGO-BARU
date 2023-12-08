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
        $estimation = EstimationMovement::create([
            'movement_type' => $type_movement,
            'reference_type' => $reference_type,
            'reference_id' => $reference,
            'product_id' => $product,
            'type' => $type,
            'movement_date' => Carbon::now()->format('d-m-Y'),
            'quantity' => $quantity
        ]);
    }

    public static function createProduction($product, $quantity, $type, $type_produksi)
    {
        $production = ProductionEstimation::where('product_id', $product)->where('type', $type)->first();

        if ($production) {
            $production->{$type_produksi} += $quantity;
            $production->estimasi = ($production->internal + $production->eksternal + max(0, $production->sales - $production->internal)) - $production->produksi;
            $production->save();
        } else {
            $new = ProductionEstimation::create([
                'product_id' => $product,
                'type' => $type,
                $type_produksi => $quantity,
                'estimasi' => $quantity,
            ]);
        }
    }

    public static function createInternal($product, $quantity, $type)
    {
        $production = ProductionEstimation::where('product_id', $product)->where('type', $type)->first();

        if ($production) {
            $production->internal += $quantity;
            $production->estimasi = ($production->internal + $production->eksternal + max(0, $production->sales - $production->internal)) - $production->produksi;
            $production->save();
        } else {
            $new = ProductionEstimation::create([
                'product_id' => $product,
                'type' => $type,
                'internal' => $quantity,
                'estimasi' => $quantity,
            ]);
        }
    }

    public static function createCetak($product, $quantity, $type)
    {
        $production = ProductionEstimation::where('product_id', $product)->where('type', $type)->first();

        if ($production) {
            $production->produksi += $quantity;
            $production->estimasi = ($production->internal + $production->eksternal + max(0, $production->sales - $production->internal)) - $production->produksi;
            $production->save();
        }
    }

    public static function createUpdateRealisasi($product, $quantity)
    {
        $production = ProductionEstimation::where('product_id', $product)->first();

        if ($production) {
            $production->realisasi += $quantity;
            $production->save();
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

        if ($estimation) {
            $estimation->movement_date = Carbon::now()->format('d-m-Y');
            $estimation->quantity = $quantity;
            $estimation->save();
        }
    }
    //Edit movement menggunakan quantity baru
    //Edit production menggunakan quantity selisih
    public static function editProduction($product, $quantity, $type, $type_produksi)
    {
        $production = ProductionEstimation::where('product_id', $product)->where('type', $type)->first();

        if ($production) {
            $production->{$type_produksi} += $quantity;
            $production->estimasi = ($production->internal + $production->eksternal + max(0, $production->sales - $production->internal)) - $production->produksi;
            $production->save();
        }
    }

    public static function editInternal($product, $quantity, $type)
    {
        $production = ProductionEstimation::where('product_id', $product)->where('type', $type)->first();

        if ($production) {
            $production->internal += $quantity;
            $production->estimasi = ($production->internal + $production->eksternal + max(0, $production->sales - $production->internal)) - $production->produksi;
            $production->save();
        }
    }

    public static function editCetak($product, $quantity, $type)
    {
        $production = ProductionEstimation::where('product_id', $product)->where('type', $type)->first();

        if ($production) {
            $production->produksi += $quantity;
            $production->estimasi = ($production->internal + $production->eksternal + max(0, $production->sales - $production->internal)) - $production->produksi;
            $production->save();
        }
    }

    public static function updateMoved($order, $quantity) {
        $order = SalesOrder::findOrFail($order);
        $order->moved += $quantity;
        $order->save();
    }

    public static function updateRetur($order, $quantity) {
        $order = SalesOrder::findOrFail($order);
        $order->retur += $quantity;
        $order->save();
    }
}
