<?php

namespace App\Http\Requests;

use App\Models\Book;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateBookRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('book_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'nullable',
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
            'cover_id' => [
                'required',
                'integer',
            ],
            'semester_id' => [
                'required',
                'integer',
            ],
            'photo' => [
                'array',
            ],
        ];
    }
}
