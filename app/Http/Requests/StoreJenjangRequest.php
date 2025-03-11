<?php

namespace App\Http\Requests;

use App\Models\Jenjang;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreJenjangRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('jenjang_create');
    }

    public function rules()
    {
        return [
            'code' => [
                'string',
                'required',
                'unique:jenjangs',
            ],
            'name' => [
                'string',
                'required',
            ],
        ];
    }
}
