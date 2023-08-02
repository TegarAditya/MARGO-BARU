<?php
namespace App\Services;

use App\Models\BookVariant;
use App\Models\PlatePrint;

class PlatePrintService
{
    public static function createPerintahCetak($no_spk, $date, $semester, $vendor)
    {
        $print = PlatePrint::create([
            'no_spk' => $no_spk,
            'date' => $date,
            'semester_id' => $semester,
            'vendor_id' => $vendor,
            'customer' => null,
            'type' => 'internal',
            'fee' => 0,
            'note' => null,
        ]);

        $material = Material::updateOrCreate([
            'code' => $plate->code.'|'. $product->code,
        ], [
            'name' => $plate->name .' ('. $product->name .')',
            'category' => 'printed_plate',
            'unit_id' => $plate->unit_id,
            'cost' => 0,
            'stock' => DB::raw("stock + $plate_quantity"),
            'warehouse_id' => 2,
        ]);

        $material->vendors()->sync($plate->vendors->pluck('id')->toArray());

        StockService::createMovementMaterial('in', 'plating', $cetak->id, $date, $material->id, $plate_quantity);

        StockService::createMovementMaterial('out', 'plating', $cetak->id, $date, $plate->id, -1 * $plate_quantity);
        StockService::updateStockMaterial($plate->id, -1 * $plate_quantity);
        StockService::createMovementMaterial('out', 'plating', $cetak->id, $date, $chemical->id, -1 * $chemical_quantity);
        StockService::updateStockMaterial($chemical->id, -1 * $chemical_quantity);
    }


}
