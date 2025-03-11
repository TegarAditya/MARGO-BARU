<?php

namespace App\Http\Requests;

use App\Models\Jenjang;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateJenjangRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('jenjang_edit');
    }

    public function rules()
    {
        return [
            'code' => [
                'string',
                'required',
                'unique:jenjangs,code,' . request()->route('jenjang')->id,
            ],
            'name' => [
                'string',
                'required',
            ],
        ];
    }
}
