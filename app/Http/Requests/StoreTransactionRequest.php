<?php

namespace App\Http\Requests;

use App\Models\Transaction;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreTransactionRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('transaction_create');
    }

    public function rules()
    {
        return [
            'date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'semester_id' => [
                'required',
                'integer',
            ],
            'reference_id' => [
                'required',
                'integer',
            ],
            'reference_no' => [
                'string',
                'required',
            ],
            'amount' => [
                'required',
            ],
            'category' => [
                'required',
            ],
            'status' => [
                'required',
            ],
        ];
    }
}
