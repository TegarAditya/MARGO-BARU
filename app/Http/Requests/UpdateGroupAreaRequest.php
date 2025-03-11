<?php

namespace App\Http\Requests;

use App\Models\GroupArea;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateGroupAreaRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('group_area_edit');
    }

    public function rules()
    {
        return [
            'code' => [
                'string',
                'nullable',
            ],
            'name' => [
                'string',
                'required',
            ],
            'provinsi' => [
                'required',
            ],
        ];
    }
}
