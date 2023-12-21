<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\CreatedUpdatedBy;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockMovement extends Model
{
    use SoftDeletes, Auditable, HasFactory, CreatedUpdatedBy;

    public $table = 'stock_movements';

    protected $dates = [
        'movement_date',
        'reference_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const TRANSACTION_TYPE_SELECT = [
        'awal'   => 'Stock Awal',
        'adjustment' => 'Adjustment',
        'delivery'   => 'Delivery',
        'retur'      => 'Retur',
        'cetak'      => 'Cetak',
        'produksi'   => 'Produksi',
        'plating'   => 'Cetak Plate',
    ];

    public const MOVEMENT_TYPE_SELECT = [
        'in'         => 'In',
        'out'        => 'Out',
        'adjustment' => 'Adjustment',
        'revisi'     => 'Revisi'
    ];

    protected $fillable = [
        'warehouse_id',
        'movement_date',
        'movement_type',
        'transaction_type',
        'finishing_masuk',
        'reference_id',
        'reference_date',
        'product_id',
        'material_id',
        'quantity',
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

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function getMovementDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setMovementDateAttribute($value)
    {
        $this->attributes['movement_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getReferenceDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setReferenceDateAttribute($value)
    {
        $this->attributes['reference_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function reference()
    {
        if ($this->transaction_type === 'adjustment') {
            return $this->belongsTo(StockAdjustment::class, 'reference_id');
        } else if ($this->transaction_type === 'delivery') {
            return $this->belongsTo(DeliveryOrder::class, 'reference_id');
        } else if ($this->transaction_type === 'retur') {
            return $this->belongsTo(ReturnGood::class, 'reference_id');
        } else if ($this->transaction_type === 'cetak') {
            return $this->belongsTo(Cetak::class, 'reference_id');
        } else if ($this->transaction_type === 'produksi') {
            return $this->belongsTo(Finishing::class, 'reference_id');
        } else if ($this->transaction_type === 'plating') {
            return $this->belongsTo(PlatePrint::class, 'reference_id');
        }
    }

    public function product()
    {
        return $this->belongsTo(BookVariant::class, 'product_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function reversal_of()
    {
        return $this->belongsTo(self::class, 'reversal_of_id');
    }

    public function pengedit()
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    public function finishing_masuk()
    {
        return $this->belongsTo(FinishingMasuk::class, 'finishing_masuk');
    }
}
