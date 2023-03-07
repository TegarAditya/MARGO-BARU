<?php

namespace App\Http\Requests;

use App\Models\StockSaldo;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateStockSaldoRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('stock_saldo_edit');
    }

    public function rules()
    {
        return [
            'periode' => [
                'string',
                'required',
            ],
            'start_date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'end_date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'qty_awal' => [
                'numeric',
                'required',
            ],
            'in' => [
                'numeric',
                'required',
            ],
            'out' => [
                'numeric',
                'required',
            ],
            'qty_akhir' => [
                'numeric',
                'required',
            ],
        ];
    }
}
