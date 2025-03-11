<?php

namespace App\Http\Requests;

use App\Models\ProductionTransaction;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreProductionTransactionRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('production_transaction_create');
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
            'transaction_date' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
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
