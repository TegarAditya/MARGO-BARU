<?php

namespace App\Http\Requests;

use App\Models\StockAdjustmentDetail;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreStockAdjustmentDetailRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('stock_adjustment_detail_create');
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
