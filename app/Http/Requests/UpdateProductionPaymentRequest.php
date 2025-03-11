<?php

namespace App\Http\Requests;

use App\Models\ProductionPayment;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateProductionPaymentRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('production_payment_edit');
    }

    public function rules()
    {
        return [
            'no_payment' => [
                'string',
                'required',
                'unique:production_payments,no_payment,' . request()->route('production_payment')->id,
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
