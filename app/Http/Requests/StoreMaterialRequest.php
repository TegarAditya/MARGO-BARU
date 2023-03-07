<?php

namespace App\Http\Requests;

use App\Models\Material;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreMaterialRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('material_create');
    }

    public function rules()
    {
        return [
            'code' => [
                'string',
                'required',
                'unique:materials',
            ],
            'name' => [
                'string',
                'required',
            ],
            'category' => [
                'required',
            ],
            'unit_id' => [
                'required',
                'integer',
            ],
            'cost' => [
                'required',
            ],
            'stock' => [
                'numeric',
                'required',
            ],
        ];
    }
}
