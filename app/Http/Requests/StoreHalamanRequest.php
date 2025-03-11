<?php

namespace App\Http\Requests;

use App\Models\Halaman;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreHalamanRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('halaman_create');
    }

    public function rules()
    {
        return [
            'code' => [
                'string',
                'required',
                'unique:halamen',
            ],
            'name' => [
                'string',
                'required',
            ],
            'value' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
        ];
    }
}
