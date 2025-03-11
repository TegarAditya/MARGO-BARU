<?php

namespace App\Http\Requests;

use App\Models\Isi;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateIsiRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('isi_edit');
    }

    public function rules()
    {
        return [
            'code' => [
                'string',
                'required',
                'unique:isis,code,' . request()->route('isi')->id,
            ],
            'name' => [
                'string',
                'required',
            ],
        ];
    }
}
