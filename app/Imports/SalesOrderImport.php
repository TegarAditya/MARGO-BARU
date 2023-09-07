<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Book;
use App\Models\BookVariant;
use App\Models\Jenjang;
use App\Models\Semester;
use App\Models\SalesOrder;
use DB;
use Alert;
use App\Services\EstimationService;
use Carbon\Carbon;

class SalesOrderImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $salesperson = Salesperson::where('code', $row['sales'])->first();
            $semester = setting('current_semester');
            $product = BookVariant::where('code', $row['buku'])->first();
            $quantity = $row['estimasi'];

            DB::beginTransaction();
            try {
                $order = SalesOrder::updateOrCreate([
                    'semester_id' => $semester,
                    'salesperson_id' => $salesperson,

                    'product_id' => $product->id,
                    'jenjang_id' => $product->jenjang_id,
                    'kurikulum_id' => $product->kurikulum_id
                ], [
                    'no_order' => SalesOrder::generateNoOrder($semester, $salesperson),
                    'quantity' => DB::raw("quantity + $quantity"),
                    'moved' => 0,
                    'retur' => 0
                ]);

                if ($product->semester_id === $semester) {
                    EstimationService::createMovement('in', 'sales_order', $order->id, $product->id, $quantity, $product->type);
                    EstimationService::createProduction($product->id, $quantity, $product->type);

                    foreach($product->components as $item) {
                        EstimationService::createMovement('in', 'sales_order', $order->id, $item->id, $quantity, $item->type);
                        EstimationService::createProduction($item->id, $quantity, $item->type);
                    }
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                dd($e->getMessage());
                Alert::error('Error', $e->getMessage());

                return redirect()->back();
            }
        }
    }
}
