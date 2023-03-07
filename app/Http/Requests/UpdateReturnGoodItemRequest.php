<?php

namespace App\Http\Requests;

use App\Models\ReturnGoodItem;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateReturnGoodItemRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('return_good_item_edit');
    }

    public function rules()
    {
        return [
            'retur_id' => [
                'required',
                'integer',
            ],
            'salesperson_id' => [
                'required',
                'integer',
            ],
            'semester_id' => [
                'required',
                'integer',
            ],
            'product_id' => [
                'required',
                'integer',
            ],
            'price' => [
                'required',
            ],
            'quantity' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'total' => [
                'required',
            ],
            'sales_order_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
