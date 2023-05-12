<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Models\Book;
use App\Models\BookVariant;
use App\Models\Jenjang;
use App\Models\Kurikulum;
use App\Models\Mapel;
use App\Models\Kelas;
use App\Models\Cover;
use App\Models\Semester;
use App\Models\Halaman;
use DB;
use Alert;

class BookImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $code = $row['kode'];
            $jenjang = Jenjang::where('code', substr($code, 0, 3))->first();
            $kurikulum = Kurikulum::where('code', substr($code, 3, 2))->first();
            $mapel = Mapel::where('code', substr($code, 5, 3))->first();
            $kelas = Kelas::where('code', substr($code, 8, 2))->first();
            $cover = Cover::where('code', substr($code, 10, 3))->first();
            $semester = Semester::where('code', substr($code, 13, 4))->first();
            $halaman = Halaman::where('code', $row['halaman'])->first();

            DB::beginTransaction();
            try {
                $buku = Book::updateOrCreate([
                    'code' => $code
                ],
                [
                    'name' => Book::generateName($jenjang->id, $kurikulum->id, $mapel->id, $kelas->id, $cover->id, $semester->id),
                    'jenjang_id' => $jenjang->id,
                    'kurikulum_id' => $kurikulum->id,
                    'mapel_id' => $mapel->id,
                    'kelas_id' => $kelas->id,
                    'cover_id' => $cover->id,
                    'semester_id' => $semester->id,
                ]);

                foreach(BookVariant::TYPE_SELECT as $key => $label) {
                    $stock = ($key == 'L') ? $row['stok'] : 0;
                    $price = ($key == 'L') ? $row['harga'] : 0;
                    $cost = ($key == 'L') ? $row['hpp'] : 0;

                    $variant = BookVariant::updateOrCreate([
                        'book_id' => $buku->id,
                        'code' => $key . '-' .$code,
                        'type' => $key,
                    ],
                    [
                        'jenjang_id' => $jenjang->id,
                        'semester_id' => $semester->id,
                        'kurikulum_id' => $kurikulum->id,
                        'halaman_id' => $halaman->id,
                        'warehouse_id' => 1,
                        'stock' => $stock,
                        'unit_id' => 1,
                        'price' => $price,
                        'cost' => $cost,
                        'status' => 1,
                    ]);
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
