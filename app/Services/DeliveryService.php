<?php
namespace App\Services;

use App\Models\DeliveryOrder;

class DeliveryService
{
    public static function generateFaktur($delivery) {
        DeliveryOrder::where('id', $delivery)->update([
            'faktur' => 1
        ]);
    }
}
