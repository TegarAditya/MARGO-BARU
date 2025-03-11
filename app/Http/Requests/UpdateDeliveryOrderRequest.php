<?php

namespace App\Http\Requests;

use App\Models\DeliveryOrder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateDeliveryOrderRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('delivery_order_edit');
    }

    public function rules()
    {
        return [
            'no_suratjalan' => [
                'string',
                'required',
                'unique:delivery_orders,no_suratjalan,' . request()->route('delivery_order')->id,
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
