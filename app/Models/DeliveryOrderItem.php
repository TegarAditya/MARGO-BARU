<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryOrderItem extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'delivery_order_items';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'semester_id',
        'salesperson_id',
        'sales_order_id',
        'delivery_order_id',
        'product_id',
        'quantity',
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

    public function sales_order()
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id');
    }

    public function delivery_order()
    {
        return $this->belongsTo(DeliveryOrder::class, 'delivery_order_id');
    }

    public function product()
    {
        return $this->belongsTo(BookVariant::class, 'product_id');
    }

    public function invoice_item()
    {
        return $this->hasOne(InvoiceItem::class, 'delivery_order_item_id');
    }
}
