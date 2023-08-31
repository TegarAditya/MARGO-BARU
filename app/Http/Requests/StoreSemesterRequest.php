<?php

namespace App\Http\Requests;

use App\Models\Semester;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreSemesterRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('semester_create');
    }

    public function rules()
    {
        return [
            'code' => [
                'string',
                'required',
                'unique:semesters'
            ],
            'name' => [
                'string',
                'required',
            ],
            'type' => [
                'required',
            ],
            'start_date' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'end_date' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'status' => [
                'required',
            ],
        ];
    }
}
