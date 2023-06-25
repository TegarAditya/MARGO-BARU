<?php

namespace App\Models;

use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public $table = 'materials';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const CATEGORY_SELECT = [
        'paper' => 'Kertas',
        'plate' => 'Plate',
    ];

    protected $fillable = [
        'code',
        'name',
        'description',
        'category',
        'unit_id',
        'cost',
        'stock',
        'warehouse_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class);
    }
}
