<?php

namespace App\Http\Requests;

use App\Models\Kurikulum;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreKurikulumRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('kurikulum_create');
    }

    public function rules()
    {
        return [
            'code' => [
                'string',
                'required',
                'unique:kurikulums',
            ],
            'name' => [
                'string',
                'required',
            ],
        ];
    }
}
