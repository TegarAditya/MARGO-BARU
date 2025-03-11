<?php

namespace App\Http\Requests;

use App\Models\Kelas;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreKelaRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('kela_create');
    }

    public function rules()
    {
        return [
            'code' => [
                'string',
                'required',
                'unique:kelas',
            ],
            'name' => [
                'string',
                'required',
            ],
        ];
    }
}
