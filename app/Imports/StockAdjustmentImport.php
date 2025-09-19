<?php

namespace App\Imports;

use App\Models\StockAdjustment;
use App\Models\StockAdjustmentDetail;
use App\Services\StockService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StockAdjustmentImport implements ToCollection, WithHeadingRow
{
    private $adjustment;

    public function __construct(int $adjustment)
    {
        $this->adjustment = StockAdjustment::find($adjustment);
    }

    public function collection(Collection $rows)
    {
        $adjustment = $this->adjustment;
        $type = $adjustment->type;
        $date = $adjustment->date;

        foreach ($rows as $row) {
            $product    = $row['product_id'];
            $quantity   = $row['quantity'];

            $multiplier = ($adjustment->operation == 'increase') ? 1 : -1;

            if ($type === 'book') {
                StockAdjustmentDetail::create([
                    'product_id'            => $product,
                    'stock_adjustment_id'   => $adjustment->id,
                    'quantity'              => $quantity,
                ]);

                StockService::createMovement('adjustment', 'adjustment', $adjustment->id, $date, $product, $multiplier * $quantity);
                StockService::updateStock($product, $multiplier * $quantity);
            } elseif ($type === 'material') {
                StockAdjustmentDetail::create([
                    'material_id'           => $product,
                    'stock_adjustment_id'   => $adjustment->id,
                    'quantity'              => $quantity,
                ]);

                StockService::createMovementMaterial('adjustment', 'adjustment', $adjustment->id, $date, $product, $multiplier * $quantity);
                StockService::updateStockMaterial($product, $multiplier * $quantity);
            }
        }
    }
}
