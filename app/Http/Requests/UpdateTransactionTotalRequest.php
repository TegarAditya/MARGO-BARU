<?php

namespace App\Http\Requests;

use App\Models\TransactionTotal;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateTransactionTotalRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('transaction_total_edit');
    }

    public function rules()
    {
        return [
            'salesperson_id' => [
                'required',
                'integer',
            ],
            'total_invoice' => [
                'required',
            ],
            'total_diskon' => [
                'required',
            ],
            'total_retur' => [
                'required',
            ],
            'total_bayar' => [
                'required',
            ],
            'total_potongan' => [
                'required',
            ],
        ];
    }
}
