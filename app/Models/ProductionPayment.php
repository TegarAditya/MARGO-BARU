<?php

namespace App\Models;

use App\Traits\Auditable;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Date;

class ProductionPayment extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public const BULAN_ROMAWI = array(1=>"I","II","III", "IV", "V","VI","VII","VIII","IX","X", "XI","XII");

    public $table = 'production_payments';

    protected $dates = [
        'date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const PAYMENT_METHOD_SELECT = [
        'cash' => 'Cash',
        'bca'  => 'Bank BCA',
        'bri'  => 'Bank BRI',
    ];

    protected $fillable = [
        'no_payment',
        'date',
        'vendor_id',
        'semester_id',
        'nominal',
        'payment_method',
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

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public static function generateNoPayment($semester) {
        $data = self::where('semester_id', $semester)->count();
        $semester = Semester::find($semester);

        $payment_number = !$data ? 1 : ($data + 1);

        $prefix = 'FEE/'.strtoupper($semester->type).'/MMJ/'.self::BULAN_ROMAWI[Date::now()->format('n')].'/'.Date::now()->format('y').'/';
        $code = $prefix.sprintf("%06d", $payment_number);

        return $code;
    }
}
