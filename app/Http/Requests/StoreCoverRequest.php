<?php

namespace App\Http\Requests;

use App\Models\Cover;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreCoverRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('cover_create');
    }

    public function rules()
    {
        return [
            'code' => [
                'string',
                'required',
                'unique:covers',
            ],
            'name' => [
                'string',
                'required',
            ],
        ];
    }
}
