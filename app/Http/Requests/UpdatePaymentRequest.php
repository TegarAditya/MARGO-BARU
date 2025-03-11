<?php

namespace App\Http\Requests;

use App\Models\Payment;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdatePaymentRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('payment_edit');
    }

    public function rules()
    {
        return [
            'no_kwitansi' => [
                'string',
                'required',
                'unique:payments,no_kwitansi,' . request()->route('payment')->id,
            ],
            'date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'salesperson_id' => [
                'required',
                'integer',
            ],
            'semester_id' => [
                'required',
                'integer',
            ],
            'paid' => [
                'required',
            ],
            'amount' => [
                'required',
            ],
        ];
    }
}
