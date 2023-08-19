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

class EstimationImport implements ToCollection, WithHeadingRow
{
    private $estimasi;
    /**
    * @param Collection $collection
    */

    public function __construct(int $estimasi)
    {
        $this->estimasi = Estimation::find($estimasi);
    }

    public function collection(Collection $rows)
    {
        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                $product = BookVariant::where('code', $row['buku'])->first();
                $payment = $row['pembayaran'];
                $quantity = $row['estimasi'];

                $estimasi_id = $this->estimasi->id;
                $semester = $this->estimasi->semester_id;
                $salesperson = $this->estimasi->salesperson_id;

                $estimasi_item = EstimationItem::create([
                    'estimation_id' => $estimasi_id,
                    'semester_id' => $semester,
                    'salesperson_id' => $salesperson,
                    'payment_type' => $payment,
                    'product_id' => $product->id,
                    'jenjang_id' => $product->jenjang_id,
                    'kurikulum_id' => $product->kurikulum_id,
                    'quantity' => $quantity
                ]);

                $order = SalesOrder::updateOrCreate([
                    'semester_id' => $semester,
                    'salesperson_id' => $salesperson,
                    'product_id' => $product->id,
                    'jenjang_id' => $product->jenjang_id,
                    'kurikulum_id' => $product->kurikulum_id
                ], [
                    'payment_type' => $payment,
                    'no_order' => SalesOrder::generateNoOrder($semester, $salesperson, $payment),
                    'quantity' => DB::raw("quantity + $quantity"),
                ]);

                if ($product->semester_id == $semester) {
                    EstimationService::createMovement('in', 'sales_order', $estimasi_id, $product->id, $quantity, $product->type);
                    EstimationService::createProduction($product->id, $quantity, $product->type);

                    foreach($product->components as $item) {
                        EstimationService::createMovement('in', 'sales_order', $estimasi_id, $item->id, $quantity, $item->type);
                        EstimationService::createProduction($item->id, $quantity, $item->type);
                    }
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
