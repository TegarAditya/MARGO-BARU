<?php

namespace App\Http\Requests;

use App\Models\BookVariant;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateBookVariantRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('book_variant_edit');
    }

    public function rules()
    {
        return [
            'book_id' => [
                'nullable',
                'integer',
            ],
            'type' => [
                'required',
            ],
            'jenjang_id' => [
                'required',
                'integer',
            ],
            'semester_id' => [
                'required',
                'integer',
            ],
            'kurikulum_id' => [
                'required',
                'integer',
            ],
            'halaman_id' => [
                'required',
                'integer',
            ],
            'stock' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'components' => [
                'nullable',
                'array'
            ]
        ];
    }
}
