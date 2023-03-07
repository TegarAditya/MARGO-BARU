<?php

namespace App\Http\Requests;

use App\Models\Kurikulum;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateKurikulumRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('kurikulum_edit');
    }

    public function rules()
    {
        return [
            'code' => [
                'string',
                'required',
                'unique:kurikulums,code,' . request()->route('kurikulum')->id,
            ],
            'name' => [
                'string',
                'required',
            ],
        ];
    }
}
