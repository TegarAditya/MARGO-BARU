<?php

namespace App\Http\Requests;

use App\Models\StockAdjustmentDetail;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateStockAdjustmentDetailRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('stock_adjustment_detail_edit');
    }

    public function rules()
    {
        return [
            'stock_adjustment_id' => [
                'required',
                'integer',
            ],
            'quantity' => [
                'numeric',
                'required',
            ],
        ];
    }
}
