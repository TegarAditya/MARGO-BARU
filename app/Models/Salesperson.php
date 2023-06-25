<?php

namespace App\Models;

use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salesperson extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public $table = 'salespeople';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'code',
        'name',
        'marketing_area_id',
        'phone',
        'company',
        'address',
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

    public function marketing_area()
    {
        return $this->belongsTo(MarketingArea::class, 'marketing_area_id');
    }

    public function estimasi()
    {
        return $this->hasMany(SalesOrder::class);
    }

    public function getFullNameAttribute()
    {
        $name = $this->code. ' - '. $this->name. ' - '. $this->marketing_area?->name;

        return $name;
    }
}
