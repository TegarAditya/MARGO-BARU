<?php

namespace App\Models;

use App\Traits\Auditable;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionTransaction extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public $table = 'production_transactions';

    public const CATEGORY_SELECT = [
        'credit' => 'Kredit',
        'debet'  => 'Debet',
    ];

    protected $dates = [
        'date',
        'transaction_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const TYPE_SELECT = [
        'cetak'     => 'Faktur',
        'finishing' => 'Finishing',
        'bayar'     => 'Pembayaran',
    ];

    protected $fillable = [
        'date',
        'description',
        'vendor_id',
        'semester_id',
        'type',
        'reference_id',
        'reference_no',
        'transaction_date',
        'amount',
        'category',
        'status',
        'reversal_of_id',
        'created_by_id',
        'updated_by_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function reference()
    {
        return $this->belongsTo(Invoice::class, 'reference_id');
    }

    public function getTransactionDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setTransactionDateAttribute($value)
    {
        $this->attributes['transaction_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function reversal_of()
    {
        return $this->belongsTo(Transaction::class, 'reversal_of_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function updated_by()
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }
}
