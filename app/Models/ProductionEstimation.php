<?php

namespace App\Models;

use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionEstimation extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public $table = 'production_estimations';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'product_id',
        'type',
        'estimasi',
        'isi',
        'cover',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const TYPE_SELECT = [
        'L' => 'LKS',
        'I' => 'Isi LKS',
        'C' => 'Cover LKS',
        'P' => 'Pegangan Guru',
        'S' => 'Isi PG',
        'V' => 'Cover PG',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function product()
    {
        return $this->belongsTo(BookVariant::class, 'product_id');
    }
}
