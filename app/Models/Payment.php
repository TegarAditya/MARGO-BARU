<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\CreatedUpdatedBy;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Date;

class Payment extends Model
{
    use SoftDeletes, Auditable, HasFactory, CreatedUpdatedBy;

    public $table = 'payments';

    public const BULAN_ROMAWI = array(1=>"I","II","III", "IV", "V","VI","VII","VIII","IX","X", "XI","XII");

    protected $dates = [
        'date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const PAYMENT_METHOD_SELECT = [
        'cash' => 'Kas Besar',
        'bca'  => 'Bank BCA',
        'bri'  => 'Bank BRI',
        'mandiri'  => 'Bank Mandiri',
    ];

    protected $fillable = [
        'no_kwitansi',
        'date',
        'salesperson_id',
        'semester_id',
        'semester_bayar_id',
        'paid',
        'discount',
        'amount',
        'payment_method',
        'note',
        'created_by_id',
        'updated_by_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'paid' => 'double',
        'discount' => 'double',
        'amount' => 'double',
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

    public function salesperson()
    {
        return $this->belongsTo(Salesperson::class, 'salesperson_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function semester_bayar()
    {
        return $this->belongsTo(Semester::class, 'semester_bayar_id');
    }

    public static function generateNoKwitansi($semester) {
        $data = self::where('semester_id', $semester)->count();
        $semester = Semester::find($semester);

        $payment_number = !$data ? 1 : ($data + 1);

        $prefix = 'KW/'.strtoupper($semester->type).'/MMJ/'.self::BULAN_ROMAWI[Date::now()->format('n')].'/'.Date::now()->format('y').'/';
        $code = $prefix.sprintf("%06d", $payment_number);

        return $code;
    }
}
