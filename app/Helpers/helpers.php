<?php

/**
 * Format an amount to the given currency
 *
 * @return response()
 */

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

?>
