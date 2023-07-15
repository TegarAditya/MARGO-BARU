<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Models\Book;
use App\Models\BookVariant;
use App\Models\BookComponent;
use App\Models\Jenjang;
use App\Models\Kurikulum;
use App\Models\Mapel;
use App\Models\Kelas;
use App\Models\Isi;
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
            $semester = Semester::where('code', substr($code, 10, 4))->first();
            $isi = Isi::where('code', substr($code, 15, 3))->first();
            if (substr($code, 18, 3)) {
                $cover = Cover::where('code', substr($code, 18, 3))->first();
            } else {
                $cover = Cover::where('code', substr($code, 15, 3))->first();
            }
            $halaman = Halaman::where('code', $row['halaman'])->first();

            $lks_status = $row['lks'];
            $pg_status = $row['pg'];
            $kunci_status = $row['kunci'];

            DB::beginTransaction();
            try {
                $buku = Book::updateOrCreate([
                    'code' => $code
                ],
                [
                    'name' => Book::generateName($jenjang->id, $kurikulum->id, $mapel->id, $kelas->id, $semester->id, $isi->id, $cover->id),
                    'jenjang_id' => $jenjang->id,
                    'kurikulum_id' => $kurikulum->id,
                    'mapel_id' => $mapel->id,
                    'kelas_id' => $kelas->id,
                    'isi_id' => $isi->id,
                    'cover_id' => $cover->id,
                    'semester_id' => $semester->id,
                ]);

                if ($lks_status) {
                    $lks = BookVariant::updateOrCreate([
                        'book_id' => $buku->id,
                        'code' => 'L' . '-' .$code,
                        'type' => 'L',
                    ],
                    [
                        'name' => 'LKS' . ' - '. $buku->name,
                        'jenjang_id' => $jenjang->id,
                        'kurikulum_id' => $kurikulum->id,
                        'isi_id' => $isi->id,
                        'cover_id' => $cover->id,
                        'mapel_id' => $mapel->id,
                        'kelas_id' => $kelas->id,
                        'halaman_id' => $halaman->id,
                        'semester_id' => $semester->id,
                        'warehouse_id' => 1,
                        'stock' => 0,
                        'unit_id' => 1,
                        'price' => $row['harga'],
                        'cost' => $row['hpp'],
                        'status' => 1,
                    ]);

                    foreach(BookVariant::LKS_TYPE as $key => $label) {
                        $component = BookVariant::updateOrCreate([
                            'code' => BookVariant::generateCode($key, $code),
                            'type' => $key,
                        ],
                        [
                            'name' => BookVariant::generateName($key, $jenjang->id, $kurikulum->id, $mapel->id, $kelas->id, $semester->id, $isi->id, $cover->id),
                            'jenjang_id' => $jenjang->id,
                            'kurikulum_id' => $kurikulum->id,
                            'isi_id' => ($key == 'I')  ? $isi->id : null,
                            'cover_id' => ($key == 'C') ? $cover->id : null,
                            'mapel_id' => $mapel->id,
                            'kelas_id' => $kelas->id,
                            'halaman_id' => $halaman->id,
                            'semester_id' => $semester->id,
                            'warehouse_id' => 2,
                            'stock' => 0,
                            'unit_id' => 1,
                            'price' => 0,
                            'cost' => 0,
                            'status' => 1,
                        ]);
                        $component->material_of()->syncWithoutDetaching($lks->id);
                    }
                }

                if ($pg_status) {
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
                        'isi_id' => $isi->id,
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
                        $component = BookVariant::updateOrCreate([
                            'code' => BookVariant::generateCode($key, $code),
                            'type' => $key,
                        ],
                        [
                            'name' => BookVariant::generateName($key, $jenjang->id, $kurikulum->id, $mapel->id, $kelas->id, $semester->id, $isi->id, $cover->id),
                            'jenjang_id' => $jenjang->id,
                            'kurikulum_id' => $kurikulum->id,
                            'isi_id' => ($key == 'S')  ? $isi->id : null,
                            'cover_id' => ($key == 'V') ? $cover->id : null,
                            'mapel_id' => $mapel->id,
                            'kelas_id' => $kelas->id,
                            'halaman_id' => $halaman->id,
                            'semester_id' => $semester->id,
                            'warehouse_id' => 2,
                            'stock' => 0,
                            'unit_id' => 1,
                            'price' => 0,
                            'cost' => 0,
                            'status' => 1,
                        ]);
                        $component->material_of()->syncWithoutDetaching($pg->id);
                    }
                }

                if ($kunci_status) {
                    $kunci = BookVariant::updateOrCreate([
                        'code' => BookVariant::generateCode('K', $code),
                        'type' => 'K',
                    ],
                    [
                        'book_id' => $buku->id,
                        'name' => BookVariant::generateName('K', $jenjang->id, $kurikulum->id, $mapel->id, $kelas->id, $semester->id, $isi->id, $cover->id),
                        'jenjang_id' => $jenjang->id,
                        'semester_id' => $semester->id,
                        'kurikulum_id' => $kurikulum->id,
                        'mapel_id' => $mapel->id,
                        'kelas_id' => $kelas->id,
                        'isi_id' => $isi->id,
                        'cover_id' => null,
                        'halaman_id' => $halaman->id,
                        'warehouse_id' => 1,
                        'stock' => 0,
                        'unit_id' => 1,
                        'price' => 0,
                        'cost' => 0,
                        'status' => 1,
                    ]);

                    foreach(BookVariant::KUNCI_TYPE as $key => $label) {
                        $component = BookVariant::updateOrCreate([
                            'code' => BookVariant::generateCode($key, $code),
                            'type' => $key,
                        ],
                        [
                            'name' => BookVariant::generateName($key, $jenjang->id, $kurikulum->id, $mapel->id, $kelas->id, $semester->id, $isi->id, $cover->id),
                            'jenjang_id' => $jenjang->id,
                            'kurikulum_id' => $kurikulum->id,
                            'isi_id' => ($key == 'U')  ? $isi->id : null,
                            'cover_id' => null,
                            'mapel_id' => $mapel->id,
                            'kelas_id' => $kelas->id,
                            'halaman_id' => $halaman->id,
                            'semester_id' => $semester->id,
                            'warehouse_id' => 2,
                            'stock' => 0,
                            'unit_id' => 1,
                            'price' => 0,
                            'cost' => 0,
                            'status' => 1,
                        ]);
                        $component->material_of()->syncWithoutDetaching($kunci->id);
                    }
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                dd($e);
                Alert::error('Error', $e->getMessage());

                return redirect()->back();
            }
        }
    }
}
