<?php

namespace App\Http\Requests;

use App\Models\BookComponent;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateBookComponentRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('book_component_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'nullable',
            ],
            'type' => [
                'required',
            ],
            'jenjang_id' => [
                'required',
                'integer',
            ],
            'kurikulum_id' => [
                'required',
                'integer',
            ],
            'mapel_id' => [
                'required',
                'integer',
            ],
            'kelas_id' => [
                'required',
                'integer',
            ],
            'semester_id' => [
                'required',
                'integer',
            ],
            'stock' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'components.*' => [
                'integer',
            ],
            'components' => [
                'array',
            ],
        ];
    }
}
