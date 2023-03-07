<?php

namespace App\Http\Requests;

use App\Models\DeliveryOrderItem;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyDeliveryOrderItemRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('delivery_order_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:delivery_order_items,id',
        ];
    }
}
