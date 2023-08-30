<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\CreatedUpdatedBy;

class EstimationMovement extends Model
{
    use SoftDeletes, HasFactory, CreatedUpdatedBy;

    public $table = 'estimation_movements';

    public const MOVEMENT_TYPE_SELECT = [
        'in'  => 'In',
        'out' => 'Out',
    ];

    protected $dates = [
        'movement_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const TYPE_SELECT = [
        'estimasi' => 'Estimasi',
        'sales' => 'Estimasi Sales',
        'internal' => 'Estimasi Internal',
        'produksi' => 'SPK Produksi',
        'realisasi' => 'Realisasi Produksi',
    ];

    public const REFERENCE_TYPE_SELECT = [
        'sales_order' => 'Sales Order',
        'cetak'  => 'Cetak',
        'finishing'  => 'Finishing',
    ];

    protected $fillable = [
        'movement_date',
        'movement_type',
        'reference_type',
        'reference_id',
        'product_id',
        'quantity',
        'internal',
        'type',
        'reversal_of_id',
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

    public function getMovementDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setMovementDateAttribute($value)
    {
        $this->attributes['movement_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function reference()
    {
        return $this->belongsTo(SalesOrder::class, 'reference_id');
    }

    public function product()
    {
        return $this->belongsTo(BookVariant::class, 'product_id');
    }

    public function reversal_of()
    {
        return $this->belongsTo(self::class, 'reversal_of_id');
    }
}
