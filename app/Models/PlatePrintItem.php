<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlatePrintItem extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'plate_print_items';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'plate_print_id',
        'semester_id',
        'vendor_id',
        'product_id',
        'plate_id',
        'plate_qty',
        'chemical_id',
        'chemical_qty',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function plate_print()
    {
        return $this->belongsTo(PlatePrint::class, 'plate_print_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function product()
    {
        return $this->belongsTo(BookVariant::class, 'product_id');
    }

    public function plate()
    {
        return $this->belongsTo(Material::class, 'plate_id');
    }

    public function chemical()
    {
        return $this->belongsTo(Material::class, 'chemical_id');
    }
}
