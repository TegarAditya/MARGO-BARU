<?php

namespace App\Http\Requests;

use App\Models\Salesperson;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreSalespersonRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('salesperson_create');
    }

    public function rules()
    {
        return [
            'code' => [
                'string',
                'required',
                'unique:salespeople',
            ],
            'name' => [
                'string',
                'required',
            ],
            'marketing_area_id' => [
                'required',
                'integer',
            ],
            'phone' => [
                'string',
                'nullable',
            ],
            'company' => [
                'string',
                'nullable',
            ],
        ];
    }
}
