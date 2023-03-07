<?php

namespace App\Http\Requests;

use App\Models\StockAdjustment;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreStockAdjustmentRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('stock_adjustment_create');
    }

    public function rules()
    {
        return [
            'date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'operation' => [
                'required',
            ],
            'quantity' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'reason' => [
                'string',
                'required',
            ],
        ];
    }
}
