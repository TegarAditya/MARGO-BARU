<?php

namespace App\Exports;

use App\Models\Book;
use App\Models\BookVariant;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class BookExport implements FromCollection, ShouldAutoSize
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $books = Book::where('semester_id', 9)->orderBy('jenjang_id', 'ASC')->orderBy('mapel_id', 'ASC')->orderBy('kelas_id', 'ASC')->orderBy('isi_id', 'ASC')->orderBy('cover_id', 'ASC')->get();

        $rows = collect([]);

        $rows->push([
            'kode' => 'KODE',
            'mapel' => 'MAPEL',
            'kelas' => 'KELAS',
            'jenjang' => 'JENJANG',
            'halaman' => 'HALAMAN',
            'stok' => 'STOK',
            'harga' => 'HARGA',
            'hpp' => 'HPP',
            'lks' => 'LKS',
            'pg' => 'PG',
            'halaman_pg' => 'HALAMAN_PG',
            'kunci' => 'KUNCI',
            'halaman_kunci' => 'HALAMAN_KUNCI',
        ]);

        foreach ($books as $book) {
            $buku = BookVariant::where('book_id', $book->id)->where('type', 'L')->first();
            $pg = BookVariant::where('book_id', $book->id)->where('type', 'P')->first();
            $kunci = BookVariant::where('book_id', $book->id)->where('type', 'K')->first();

            if (!$buku) {
                continue;
            }
            $row = [
                'kode' => $book->code,
                'mapel' => $book->mapel->name,
                'kelas' => $book->kelas->code,
                'jenjang' => $book->jenjang->name,
                'halaman' => $buku->halaman->code ?? '',
                'stok' => '0',
                'harga' => (string) $buku->price ?? '',
                'hpp' => (string) $buku->cost ?? '',
                'lks' => '1',
                'pg' => $pg ? '1' : '0',
                'halaman_pg' => $pg ? ($pg->halaman->code ?? '') : '',
                'kunci' => $kunci ? '1' : '0',
                'halaman_kunci' => $kunci ? ($kunci->halaman->code ?? '') : '',
            ];

            $rows->push($row);
        }

        return $rows;
    }
}
