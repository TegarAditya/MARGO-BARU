<?php

namespace App\Http\Requests;

use App\Models\BookVariant;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreBookVariantRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('book_variant_create');
    }

    public function rules()
    {
        return [
            'book_id' => [
                'required',
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
        ];
    }
}
