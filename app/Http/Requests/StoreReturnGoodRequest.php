<?php

namespace App\Http\Requests;

use App\Models\ReturnGood;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreReturnGoodRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('return_good_create');
    }

    public function rules()
    {
        return [
            'no_retur' => [
                'string',
                'required',
                'unique:return_goods',
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
