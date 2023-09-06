<?php

namespace App\Models;

use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionTotal extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public $table = 'transaction_totals';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'salesperson_id',
        'total_invoice',
        'total_diskon',
        'total_adjustment',
        'total_retur',
        'total_bayar',
        'total_potongan',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'total_invoice' => 'double',
        'total_diskon' => 'double',
        'total_adjustment' => 'double',
        'total_retur' => 'double',
        'total_bayar' => 'double',
        'total_potongan' => 'double',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function salesperson()
    {
        return $this->belongsTo(Salesperson::class, 'salesperson_id');
    }
}
