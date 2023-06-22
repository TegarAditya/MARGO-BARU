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

if (! function_exists('angka')) {
    function angka($angka)
    {
        return number_format($angka,0,',','.');
    }
}


?>
