<?php

namespace App\Models;

use App\Traits\Auditable;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Date;

class Invoice extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public $table = 'invoices';

    public const BULAN_ROMAWI = array(1=>"I","II","III", "IV", "V","VI","VII","VIII","IX","X", "XI","XII");

    protected $dates = [
        'date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'no_faktur',
        'date',
        'delivery_order_id',
        'semester_id',
        'salesperson_id',
        'total',
        'discount',
        'nominal',
        'note',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function delivery_order()
    {
        return $this->belongsTo(DeliveryOrder::class, 'delivery_order_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function salesperson()
    {
        return $this->belongsTo(Salesperson::class, 'salesperson_id');
    }

    public static function generateNoInvoice($semester) {
        $data = self::where('semester_id', $semester)->count();
        $semester = Semester::find($semester);

        $invoice_number = !$data ? 1 : ($data + 1);

        $prefix = 'INV/'.strtoupper($semester->type).'/MMJ/'.self::BULAN_ROMAWI[Date::now()->format('n')].'/'.Date::now()->format('y').'/';
        $code = $prefix.sprintf("%04d", $invoice_number);

        return $code;
    }
}
