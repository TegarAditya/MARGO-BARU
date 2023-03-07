<?php

namespace App\Http\Requests;

use App\Models\Mapel;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreMapelRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('mapel_create');
    }

    public function rules()
    {
        return [
            'code' => [
                'string',
                'required',
                'unique:mapels',
            ],
            'name' => [
                'string',
                'required',
            ],
        ];
    }
}
