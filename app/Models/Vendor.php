<?php

namespace App\Models;

use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public $table = 'vendors';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const TYPE_SELECT = [
        'cetak'     => 'Cetak',
        'finishing' => 'Finishing',
    ];

    protected $fillable = [
        'code',
        'name',
        'type',
        'contact',
        'address',
        'company',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = [
        'full_name',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getFullNameAttribute()
    {
        $name = $this->code. ' - '. $this->name;

        return $name;
    }

    public function transactions()
    {
        return $this->hasMany(ProductionTransaction::class, 'vendor_id');
    }

    public function fee()
    {
        return $this->hasOne(ProductionTransactionTotal::class, 'vendor_id');
    }

    public function cetaks()
    {
        return $this->hasMany(Cetak::class, 'vendor_id');
    }

    public function finishings()
    {
        return $this->hasMany(Finishing::class, 'vendor_id');
    }
}
