<?php

namespace App\Models;

use App\Traits\Auditable;
use Carbon\Carbon;
use App\Traits\CreatedUpdatedBy;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Date;

class PlatePrint extends Model
{
    use SoftDeletes, Auditable, HasFactory, CreatedUpdatedBy;

    public $table = 'plate_prints';

    public const BULAN_ROMAWI = array(1=>"I","II","III", "IV", "V","VI","VII","VIII","IX","X", "XI","XII");

    protected $dates = [
        'date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const TYPE_SELECT = [
        'internal' => 'Internal',
        'external' => 'External',
    ];

    protected $fillable = [
        'no_spk',
        'date',
        'semester_id',
        'vendor_id',
        'customer',
        'type',
        'fee',
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

    public function details()
    {
        return $this->hasMany(PlatePrintItem::class, 'plate_print_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function updated_by()
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    public static function generateNoSPK($semester) {
        $data = self::where('semester_id', $semester)->withTrashed()->count();
        $semester = Semester::find($semester);

        $no = !$data ? 1 : ($data + 1);

        $prefix = 'SPK.P/MMJ/'.self::BULAN_ROMAWI[Date::now()->format('n')].'/'.strtoupper($semester->code).'/';
        $code = $prefix.sprintf("%06d", $no);

        return $code;
    }
}
