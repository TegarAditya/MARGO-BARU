<?php

namespace App\Http\Requests;

use App\Models\BillAdjustment;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateBillAdjustmentRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('bill_adjustment_edit');
    }

    public function rules()
    {
        return [
            'no_adjustment' => [
                'string',
                'required',
                'unique:bill_adjustments,no_adjustment,' . request()->route('bill_adjustment')->id,
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
