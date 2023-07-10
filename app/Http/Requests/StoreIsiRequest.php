<?php

namespace App\Http\Requests;

use App\Models\Isi;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreIsiRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('isi_create');
    }

    public function rules()
    {
        return [
            'code' => [
                'string',
                'required',
            ],
            'name' => [
                'string',
                'required',
            ],
        ];
    }
}
