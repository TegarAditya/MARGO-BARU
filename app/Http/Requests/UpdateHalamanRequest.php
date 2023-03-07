<?php

namespace App\Http\Requests;

use App\Models\Halaman;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateHalamanRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('halaman_edit');
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
        ];
    }
}
