<?php

namespace App\Http\Requests;

use App\Models\Estimation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateEstimationRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('estimation_edit');
    }

    public function rules()
    {
        return [
            'no_estimasi' => [
                'string',
                'required',
                'unique:estimations,no_estimasi,' . request()->route('estimation')->id,
            ],
            'date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'semester_id' => [
                'required',
                'integer',
            ],
            'salesperson_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
