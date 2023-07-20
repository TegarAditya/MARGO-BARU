<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesReportDetail extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'sales_report_details';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'sales_report_id',
        'type',
        'amount',
        'debet',
        'kredit',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const TYPE_SELECT = [
        'invoice'  => 'Pengambilan',
        'diskon'   => 'Diskon',
        'retur'    => 'Retur',
        'bayar'    => 'Bayar',
        'potongan' => 'Potongan',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function sales_report()
    {
        return $this->belongsTo(SalesReport::class, 'sales_report_id');
    }
}
