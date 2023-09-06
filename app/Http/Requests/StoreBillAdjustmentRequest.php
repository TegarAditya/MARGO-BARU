<?php

namespace App\Http\Requests;

use App\Models\BillAdjustment;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreBillAdjustmentRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('bill_adjustment_create');
    }

    public function rules()
    {
        return [
            'no_adjustment' => [
                'string',
                'required',
                'unique:bill_adjustments',
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
            'amount' => [
                'required',
            ],
        ];
    }
}
