<?php

namespace App\Http\Requests;

use App\Models\Isi;
use Illuminate\Support\Facades\Gate;
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
                'unique:isis',
            ],
            'name' => [
                'string',
                'required',
            ],
        ];
    }
}
