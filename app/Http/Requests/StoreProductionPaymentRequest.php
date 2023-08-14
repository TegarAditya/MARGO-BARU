<?php

namespace App\Http\Requests;

use App\Models\ProductionPayment;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreProductionPaymentRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('production_payment_create');
    }

    public function rules()
    {
        return [
            'no_payment' => [
                'string',
                'required',
                'unique:production_payments',
            ],
            'date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'vendor_id' => [
                'required',
                'integer',
            ],
            'semester_id' => [
                'required',
                'integer',
            ],
            'nominal' => [
                'required',
            ],
        ];
    }
}
