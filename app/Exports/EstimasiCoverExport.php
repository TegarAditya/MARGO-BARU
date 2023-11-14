<?php

namespace App\Exports;

use App\Models\BookVariant;
use App\Models\Cover;
use App\Models\Isi;
use App\Models\ProductionEstimation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class EstimasiCoverExport implements FromCollection, ShouldAutoSize
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $production_estimations = ProductionEstimation::with(['product'])
                    ->whereHas('product', function ($q) {
                            $q->where('semester_id', setting('current_semester'))->where('type', 'L');
                    })->get();

        $products = BookVariant::whereHas('estimasi_produksi')->with('jenjang', 'kurikulum', 'mapel', 'kelas', 'halaman')->distinct()->get(['jenjang_id', 'kurikulum_id', 'mapel_id', 'kelas_id', 'halaman_id']);

        $covers = Cover::whereHas('production_estimations')->get();

        $isis = Isi::whereHas('production_estimations')->get();

        $rows = collect([]);

        $label = ['No.', 'Mapel', 'Kelas', 'Halaman'];

        foreach($isis as $isi) {
            array_push($label, 'Naskah '. $isi->code.' Estimasi');
            array_push($label, 'Naskah '. $isi->code.' Produksi');
        }

        array_push($label, ' ');

        foreach($covers as $cover) {
            array_push($label, 'Cover '. $cover->code .' Estimasi');
            array_push($label, 'Cover '. $cover->code .' Produksi');
        }
        $rows->push($label);

        $i = 0;

        foreach($products as $product) {
            $i++;
            $item = [$i, $product->mapel->name, (string) $product->kelas?->code, (string) $product->halaman?->code];

            $product_filter =  $production_estimations->where('product.jenjang_id', $product->jenjang_id)
                    ->where('product.kurikulum_id', $product->kurikulum_id)
                    ->where('product.mapel_id', $product->mapel_id)
                    ->where('product.kelas_id', $product->kelas_id)
                    ->where('product.halaman_id', $product->halaman_id);

            foreach($isis as $isi) {
                $isi_filter = $product_filter->where('product.isi_id', $isi->id);
                $estimasi = $isi_filter->sum('estimasi');
                $produksi = $isi_filter->sum('produksi');
                if ($estimasi <= 0) {
                    $estimasi = $isi_filter->sum('sales') - $produksi;
                }
                array_push($item, (string) $estimasi);
                array_push($item, (string) $produksi);
            }

            array_push($item, ' ');

            foreach($covers as $cover) {
                $cover_filter = $product_filter->where('product.cover_id', $cover->id);
                $estimasi = $cover_filter->sum('estimasi');
                $produksi = $cover_filter->sum('produksi');

                if ($estimasi <= 0) {
                    $estimasi = $cover_filter->sum('sales')  - $produksi;
                }
                array_push($item, (string) $estimasi);
                array_push($item, (string) $produksi);
            }

            $rows->push($item);
        }

        return $rows;
    }
}
