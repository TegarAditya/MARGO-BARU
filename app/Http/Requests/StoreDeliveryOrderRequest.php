<?php

namespace App\Http\Requests;

use App\Models\DeliveryOrder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreDeliveryOrderRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('delivery_order_create');
    }

    public function rules()
    {
        return [
            'no_suratjalan' => [
                'string',
                'required',
                'unique:delivery_orders',
            ],
            'date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'semester_id' => [
                'required',
                'integer',
            ],
            'salesperson_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
