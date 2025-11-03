<?php

namespace App\Imports;

use App\Models\BookVariant;
use App\Models\Material;
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
            $code    = $row['code'];
            $quantity   = $row['quantity'];

            $multiplier = ($adjustment->operation == 'increase') ? 1 : -1;

            if ($type === 'book') {
                $product = BookVariant::where('code', $code)->first();
            } elseif ($type === 'material') {
                $product = Material::where('code', $code)->first();
            }

            if (! $product) {
                throw new \Exception("Product code not found: " . $code);
            }

            $productId = $product->id;

            if ($type === 'book') {
                StockAdjustmentDetail::create([
                    'product_id'            => $productId,
                    'stock_adjustment_id'   => $adjustment->id,
                    'quantity'              => $quantity,
                ]);

                StockService::createMovement('adjustment', 'adjustment', $adjustment->id, $date, $productId, $multiplier * $quantity);
                StockService::updateStock($productId, $multiplier * $quantity);
            } elseif ($type === 'material') {
                StockAdjustmentDetail::create([
                    'material_id'           => $productId,
                    'stock_adjustment_id'   => $adjustment->id,
                    'quantity'              => $quantity,
                ]);

                StockService::createMovementMaterial('adjustment', 'adjustment', $adjustment->id, $date, $productId, $multiplier * $quantity);
                StockService::updateStockMaterial($productId, $multiplier * $quantity);
            }
        }
    }
}
