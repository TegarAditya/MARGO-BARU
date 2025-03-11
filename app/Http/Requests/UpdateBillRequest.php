<?php

namespace App\Http\Requests;

use App\Models\Bill;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateBillRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('bill_edit');
    }

    public function rules()
    {
        return [
            'code' => [
                'string',
                'required',
                'unique:bills,code,' . request()->route('bill')->id,
            ],
            'semester_id' => [
                'required',
                'integer',
            ],
            'salesperson_id' => [
                'required',
                'integer',
            ],
            'start_date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'end_date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'saldo_awal' => [
                'required',
            ],
            'saldo_akhir' => [
                'required',
            ],
        ];
    }
}
