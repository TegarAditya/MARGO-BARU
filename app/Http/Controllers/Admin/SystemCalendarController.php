<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;

class SystemCalendarController extends Controller
{
    public $sources = [
        [
            'model'      => '\App\Models\DeliveryOrder',
            'date_field' => 'date',
            'field'      => 'no_suratjalan',
            'prefix'     => 'Surat Jalan No',
            'suffix'     => 'Dibuat',
            'route'      => 'admin.delivery-orders.edit',
        ],
        [
            'model'      => '\App\Models\Invoice',
            'date_field' => 'date',
            'field'      => 'no_faktur',
            'prefix'     => 'Faktur dengan No',
            'suffix'     => 'Dibuat',
            'route'      => 'admin.invoices.edit',
        ],
        [
            'model'      => '\App\Models\ReturnGood',
            'date_field' => 'date',
            'field'      => 'no_retur',
            'prefix'     => 'Retur dengan No',
            'suffix'     => 'Dibuat',
            'route'      => 'admin.return-goods.edit',
        ],
        [
            'model'      => '\App\Models\Payment',
            'date_field' => 'date',
            'field'      => 'no_kwitansi',
            'prefix'     => 'Pembayaran dengan No',
            'suffix'     => 'Dibuat',
            'route'      => 'admin.payments.edit',
        ],
    ];

    public function index()
    {
        $events = [];
        foreach ($this->sources as $source) {
            foreach ($source['model']::all() as $model) {
                $crudFieldValue = $model->getAttributes()[$source['date_field']];

                if (! $crudFieldValue) {
                    continue;
                }

                $events[] = [
                    'title' => trim($source['prefix'] . ' ' . $model->{$source['field']} . ' ' . $source['suffix']),
                    'start' => $crudFieldValue,
                    'url'   => route($source['route'], $model->id),
                ];
            }
        }

        return view('admin.calendar.calendar', compact('events'));
    }
}
