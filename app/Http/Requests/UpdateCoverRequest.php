<?php

namespace App\Http\Requests;

use App\Models\Cover;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateCoverRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('cover_edit');
    }

    public function rules()
    {
        return [
            'code' => [
                'string',
                'required',
                'unique:covers,code,' . request()->route('cover')->id,
            ],
            'name' => [
                'string',
                'required',
            ],
        ];
    }
}
