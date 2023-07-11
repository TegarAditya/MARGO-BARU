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

class Finishing extends Model
{
    use SoftDeletes, Auditable, HasFactory, CreatedUpdatedBy;

    public const BULAN_ROMAWI = array(1=>"I","II","III", "IV", "V","VI","VII","VIII","IX","X", "XI","XII");

    public $table = 'finishings';

    protected $dates = [
        'date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'no_spk',
        'date',
        'semester_id',
        'vendor_id',
        'total_cost',
        'estimasi_oplah',
        'total_oplah',
        'note',
        'created_by_id',
        'updated_by_id',
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

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public static function generateNoSPK($semester, $vendor) {
        $data = self::where('semester_id', $semester)->count();
        $semester = Semester::find($semester);
        $vendor = Vendor::find($vendor);

        $no = !$data ? 1 : ($data + 1);

        $prefix = 'SPK.F/'. $vendor->code .'/'.self::BULAN_ROMAWI[Date::now()->format('n')].'/'.strtoupper($semester->code).'/';
        $code = $prefix.sprintf("%06d", $no);

        return $code;
    }
}
