<?php

namespace App\Models;

use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupArea extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public $table = 'group_areas';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'code',
        'name',
        'provinsi',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const PROVINSI_SELECT = [
        'jateng' => 'Jawa Tengah',
        'jatim'  => 'Jawa Timur',
        'jabar'  => 'Jawa Barat',
        'luar_jawa'  => 'Luar Jawa',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function marketing_areas()
    {
        return $this->hasMany(MarketingArea::class, 'group_area_id');
    }
}
