<?php

namespace App\Http\Requests;

use App\Models\DeliveryOrderItem;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateDeliveryOrderItemRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('delivery_order_item_edit');
    }

    public function rules()
    {
        return [
            'semester_id' => [
                'required',
                'integer',
            ],
            'salesperson_id' => [
                'required',
                'integer',
            ],
            'sales_order_id' => [
                'required',
                'integer',
            ],
            'delivery_order_id' => [
                'required',
                'integer',
            ],
            'product_id' => [
                'required',
                'integer',
            ],
            'quantity' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
        ];
    }
}
