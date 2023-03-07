<?php

namespace App\Http\Requests;

use App\Models\StockAdjustmentDetail;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyStockAdjustmentDetailRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('stock_adjustment_detail_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:stock_adjustment_details,id',
        ];
    }
}
