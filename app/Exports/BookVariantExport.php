<?php

namespace App\Exports;

use App\Models\Book;
use App\Models\BookVariant;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class BookVariantExport implements FromCollection, ShouldAutoSize
{
    use Exportable;

    private Collection $book;

    public function __construct(Collection $book)
    {
        $this->book = $book;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $rows = collect([]);

        $rows->push([
            'no' => 'NO',
            'kode' => 'KODE',
            'nama' => 'NAMA',
            'jenis' => 'JENIS',
            'jenis_c' => 'KODE JENIS',
            'mapel' => 'MAPEL',
            'mapel_c' => 'KODE MAPEL',
            'jenjang' => 'JENJANG',
            'jenjang_c' => 'KODE JENJANG',
            'kurikulum' => 'KURIKULUM',
            'kurikulum_c' => 'KODE KURIKULUM',
            'kelas' => 'KELAS',
            'kelas_c' => 'KODE KELAS',
            'semester' => 'SEMESTER',
            'semester_c' => 'KODE SEMESTER',
            'halaman' => 'HALAMAN',
            'isi' => 'ISI',
            'isi_c' => 'KODE ISI',
            'cover' => 'COVER',
            'cover_c' => 'KODE COVER',
            'created_at' => 'CREATED AT',
        ]);

        $i = 0;

        foreach ($this->book as $book) {
            $i++;

            $row = [
                'no' => $i,
                'kode' => $book->code,
                'nama' => $book->name,
                'jenis' => BookVariant::TYPE_SELECT[$book->type],
                'jenis_c' => $book->type,
                'mapel' => $book->mapel->name ?? '',
                'mapel_c' => $book->mapel->code ?? '',
                'jenjang' => $book->jenjang->name ?? '',
                'jenjang_c' => $book->jenjang->code ?? '',
                'kurikulum' => $book->kurikulum->name ?? '',
                'kurikulum_c' => $book->kurikulum->code ?? '',
                'kelas' => $book->kelas->name ?? '',
                'kelas_c' => $book->kelas->code ?? '',
                'semester' => $book->semester->name ?? '',
                'semester_c' => $book->semester->code ?? '',
                'halaman' => $book->halaman->name ?? '',
                'isi' => $book->isi->name ?? '',
                'isi_c' => $book->isi->code ?? '',
                'cover' => $book->cover->name ?? '',
                'cover_c' => $book->cover->code ?? '',
                'created_at' => $book->created_at ?? '',
            ];

            $rows->push($row);
        }

        return $rows;
    }
}
