<?php

namespace App\Http\Requests;

use App\Models\Semester;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateSemesterRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('semester_edit');
    }

    public function rules()
    {
        return [
            'code' => [
                'string',
                'required',
                'unique:semesters,code,' . request()->route('semester')->id,
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
