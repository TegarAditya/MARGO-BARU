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
            $semester = Semester::where('code', $row['semester'])->first();
            $salesperson = Salesperson::where('code', $row['sales'])->first();
            $product = BookVariant::where('code', $row['buku'])->first();
            $payment_type = $row['pembayaran'];
            $quantity = $row['estimasi'];

            DB::beginTransaction();
            try {
                $order = SalesOrder::updateOrCreate([
                    'semester_id' => $semester,
                    'salesperson_id' => $salesperson,
                    'payment_type' => SalesOrder::PAYMENT_TYPE_SELECT[$payment_type],
                    'product_id' => $product->id,
                    'jenjang_id' => $product->jenjang_id,
                    'kurikulum_id' => $product->kurikulum_id
                ], [
                    'quantity' => DB::raw("quantity + $quantity"),
                    'moved' => 0,
                    'retur' => 0
                ]);

                EstimationService::createMovement('in', 'sales_order', $order->id, $product->id, $quantity, $product->type);
                EstimationService::createProduction($product->id, $quantity, $product->type);

                foreach($product->child as $item) {
                    EstimationService::createMovement('in', 'sales_order', $order->id, $item->id, $quantity, $item->type);
                    EstimationService::createProduction($item->id, $quantity, $item->type);
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
