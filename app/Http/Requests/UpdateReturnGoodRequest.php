<?php

namespace App\Http\Requests;

use App\Models\ReturnGood;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateReturnGoodRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('return_good_edit');
    }

    public function rules()
    {
        return [
            'no_retur' => [
                'string',
                'required',
                'unique:return_goods,no_retur,' . request()->route('return_good')->id,
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
            'nominal' => [
                'required',
            ],
        ];
    }
}
