<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CetakItem extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'cetak_items';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'semester_id',
        'product_id',
        'halaman_id',
        'quantity',
        'cost',
        'plate_id',
        'plate_cost',
        'paper_id',
        'paper_cost',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function product()
    {
        return $this->belongsTo(BookVariant::class, 'product_id');
    }

    public function halaman()
    {
        return $this->belongsTo(Halaman::class, 'halaman_id');
    }

    public function plate()
    {
        return $this->belongsTo(Material::class, 'plate_id');
    }

    public function paper()
    {
        return $this->belongsTo(Material::class, 'paper_id');
    }
}
