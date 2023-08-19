<?php

namespace App\Http\Requests;

use App\Models\Estimation;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreEstimationRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('estimation_create');
    }

    public function rules()
    {
        return [
            'no_estimasi' => [
                'string',
                'required',
                'unique:estimations',
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
            'payment_type' => [
                'required',
            ],
        ];
    }
}
