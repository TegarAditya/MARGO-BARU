<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinishingItem extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'finishing_items';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'finishing_id',
        'semester_id',
        'product_id',
        'estimasi',
        'quantity',
        'cost',
        'done',
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

    public function buku()
    {
        return $this->belongsTo(Book::class, 'buku_id');
    }

    public function product()
    {
        return $this->belongsTo(BookVariant::class, 'product_id');
    }

    public function finishing()
    {
        return $this->belongsTo(Finishing::class, 'finishing_id');
    }
}
