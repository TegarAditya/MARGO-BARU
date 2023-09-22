<?php

/**
 * Format an amount to the given currency
 *
 * @return response()
 */

use App\Models\Setting;
use App\Models\Semester;
use App\Models\DeliveryOrder;
use App\Models\SalesOrder;
use App\Models\Cetak;
use App\Models\CetakItem;


if (! function_exists('formatCurrency')) {
    function formatCurrency($amount, $currency)
    {
        $fmt = new NumberFormatter( 'id_ID', NumberFormatter::CURRENCY );
        $fmt->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);
        return $fmt->formatCurrency($amount, $currency);
    }
}

if (! function_exists('money')) {
    function money($amount)
    {
        // return formatCurrency($amount, 'IDR');
        return 'Rp '. number_format($amount,0,',','.');
    }
}

if (! function_exists('setting')) {
    function setting($setting)
    {
        return App\Models\Setting::key($setting);
    }
}

if (! function_exists('angka')) {
    function angka($angka)
    {
        return number_format($angka,0,',','.');
    }
}

if (! function_exists('penyebut')) {
    function penyebut($nilai) {
        $nilai = abs($nilai);
        $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " ". $huruf[$nilai];
        } else if ($nilai <20) {
            $temp = penyebut($nilai - 10). " belas";
        } else if ($nilai < 100) {
            $temp = penyebut($nilai/10)." puluh". penyebut($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " seratus" . penyebut($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = penyebut($nilai/100) . " ratus" . penyebut($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " seribu" . penyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = penyebut($nilai/1000) . " ribu" . penyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = penyebut($nilai/1000000) . " juta" . penyebut($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = penyebut($nilai/1000000000) . " milyar" . penyebut(fmod($nilai,1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = penyebut($nilai/1000000000000) . " trilyun" . penyebut(fmod($nilai,1000000000000));
        }
        return $temp;
    }
}

if (! function_exists('terbilang')) {
    function terbilang($nilai) {
        if($nilai<0) {
            $hasil = "minus ". trim(penyebut($nilai));
        } else {
            $hasil = trim(penyebut($nilai));
        }
        return $hasil;
    }
}

if (! function_exists('costFinishing')) {
    function costFinishing($halaman, $quantity)
    {
        if ($halaman <= 64) {
            return 45 * $quantity;
        }
        if ($halaman <= 80) {
            return 50 * $quantity;
        }
        if ($halaman <= 96) {
            return 55 * $quantity;
        }
        if ($halaman <= 112) {
            return 60 * $quantity;
        }
        if ($halaman <= 128) {
            return 65 * $quantity;
        }

    }
}

if (! function_exists('prevSemester')) {
    function prevSemester($semester_id)
    {
        $semester = Semester::find($semester_id);

        $satu = substr($semester->code, 0, 2);
        $dua = substr($semester->code, 2, 4);

        if ($satu == '01') {
            $gasal = '02';
            $tahun = $dua - 1;
        } else {
            $gasal = '01';
            $tahun = $dua;
        }

        return Semester::where('code', $gasal . $tahun)->first()->id;
    }
}

if (! function_exists('checkInvoice')) {
    function checkInvoice()
    {
        $fakturs = DeliveryOrder::where('faktur', 0)->count();

        return $fakturs;
    }
}

if (! function_exists('isRetur')) {
    function isRetur($no_order)
    {
        $retur = SalesOrder::where('no_order', $no_order)->sum('retur');

        return $retur > 0 ? true : false;
    }
}

if (! function_exists('noRevisi')) {
    function noRevisi($no)
    {
        if (!str_contains($no, 'REV')) {
            return $no .= '/REV01';
        } else {
            $prefix = substr($no, 0, -2);
            $angka = substr($no, -2);
            $angka = intval($angka) + 1;
            return $prefix.sprintf("%02d", $angka);
        }
    }
}

if (! function_exists('cetakDone')) {
    function cetakDone($cetak_id)
    {
        $cetak_items = CetakItem::where('cetak_id', $cetak_id)->get();
        $cetakItemStatus = $cetak_items->where('done', '=', 1)->count();

        if ($cetakItemStatus == 0) {
            return 'danger';
        } elseif ($cetakItemStatus == $cetak_items->count()) {
            return 'success';
        } else {
            return 'warning';
        }
    }
}

?>
