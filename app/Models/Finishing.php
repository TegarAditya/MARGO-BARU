<?php

namespace App\Models;

use App\Traits\Auditable;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Finishing extends Model
{
    use SoftDeletes, Auditable, HasFactory;

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
}
