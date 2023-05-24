<?php

namespace App\Models;

use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookVariant extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public $table = 'book_variants';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const TYPE_SELECT = [
        'L' => 'LKS',
        'C' => 'Cover LKS',
        'I' => 'Isi LKS',
        'P' => 'Pegangan Guru',
        'V' => 'Cover PG',
        'S' => 'Isi PG',
    ];

    public const LKS_TYPE = [
        'C' => 'Cover LKS',
        'I' => 'Isi LKS',
    ];

    public const PG_TYPE = [
        'V' => 'Cover PG',
        'S' => 'Isi PG',
    ];

    protected $fillable = [
        'book_id',
        'parent_id',
        'code',
        'type',
        'jenjang_id',
        'semester_id',
        'kurikulum_id',
        'halaman_id',
        'warehouse_id',
        'stock',
        'unit_id',
        'price',
        'cost',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = [
        'name',
    ];

    protected $casts = [
        'price' => 'double',
        'cost' => 'double',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getNameAttribute()
    {
        $name = $this->book->name ?? '-';

        return $name;
    }

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function jenjang()
    {
        return $this->belongsTo(Jenjang::class, 'jenjang_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class, 'kurikulum_id');
    }

    public function halaman()
    {
        return $this->belongsTo(Halaman::class, 'halaman_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}
