<?php

namespace App\Http\Requests;

use App\Models\TransactionTotal;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreTransactionTotalRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('transaction_total_create');
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
