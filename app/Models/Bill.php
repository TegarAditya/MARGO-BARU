<?php

namespace App\Models;

use App\Traits\Auditable;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bill extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public $table = 'bills';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'semester_id',
        'salesperson_id',
        'previous_id',
        'saldo_awal',
        'jual',
        'diskon',
        'retur',
        'bayar',
        'potongan',
        'saldo_akhir',
        'tagihan',
        'pembayaran',
        'piutang',
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

    public function salesperson()
    {
        return $this->belongsTo(Salesperson::class, 'salesperson_id');
    }

    public function previous()
    {
        return $this->belongsTo(self::class, 'previous_id');
    }
}
