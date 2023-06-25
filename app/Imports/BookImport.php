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

                $lks = BookVariant::updateOrCreate([
                    'book_id' => $buku->id,
                    'code' => 'L' . '-' .$code,
                    'type' => 'L',
                ],
                [
                    'name' => 'LKS' . ' - '. $buku->name,
                    'jenjang_id' => $jenjang->id,
                    'semester_id' => $semester->id,
                    'kurikulum_id' => $kurikulum->id,
                    'mapel_id' => $mapel->id,
                    'kelas_id' => $kelas->id,
                    'cover_id' => $cover->id,
                    'halaman_id' => $halaman->id,
                    'warehouse_id' => 1,
                    'stock' => $row['stok'],
                    'unit_id' => 1,
                    'price' => $row['harga'],
                    'cost' => $row['hpp'],
                    'status' => 1,
                ]);

                foreach(BookVariant::LKS_TYPE as $key => $label) {
                    $variant = BookVariant::updateOrCreate([
                        'book_id' => $buku->id,
                        'code' => $key . '-' .$code,
                        'type' => $key,
                    ],
                    [
                        'name' => BookVariant::TYPE_SELECT[$key] . ' - '. $buku->name,
                        'parent_id' => $lks->id,
                        'jenjang_id' => $jenjang->id,
                        'semester_id' => $semester->id,
                        'kurikulum_id' => $kurikulum->id,
                        'mapel_id' => $mapel->id,
                        'kelas_id' => $kelas->id,
                        'cover_id' => $cover->id,
                        'halaman_id' => $halaman->id,
                        'warehouse_id' => 1,
                        'stock' => 0,
                        'unit_id' => 1,
                        'price' => 0,
                        'cost' => 0,
                        'status' => 1,
                    ]);
                }

                $pg = BookVariant::updateOrCreate([
                    'book_id' => $buku->id,
                    'code' => 'P' . '-' .$code,
                    'type' => 'P',
                ],
                [
                    'name' => 'Pegangan Guru' . ' - '. $buku->name,
                    'jenjang_id' => $jenjang->id,
                    'semester_id' => $semester->id,
                    'kurikulum_id' => $kurikulum->id,
                    'mapel_id' => $mapel->id,
                    'kelas_id' => $kelas->id,
                    'cover_id' => $cover->id,
                    'halaman_id' => $halaman->id,
                    'warehouse_id' => 1,
                    'stock' => 0,
                    'unit_id' => 1,
                    'price' => 0,
                    'cost' => 0,
                    'status' => 1,
                ]);

                foreach(BookVariant::PG_TYPE as $key => $label) {
                    $variant = BookVariant::updateOrCreate([
                        'book_id' => $buku->id,
                        'code' => $key . '-' .$code,
                        'type' => $key,
                    ],
                    [
                        'name' => BookVariant::TYPE_SELECT[$key] . ' - '. $buku->name,
                        'parent_id' => $pg->id,
                        'jenjang_id' => $jenjang->id,
                        'semester_id' => $semester->id,
                        'kurikulum_id' => $kurikulum->id,
                        'mapel_id' => $mapel->id,
                        'kelas_id' => $kelas->id,
                        'cover_id' => $cover->id,
                        'halaman_id' => $halaman->id,
                        'warehouse_id' => 1,
                        'stock' => 0,
                        'unit_id' => 1,
                        'price' => 0,
                        'cost' => 0,
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
