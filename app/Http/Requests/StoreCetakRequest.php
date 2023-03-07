<?php

namespace App\Http\Requests;

use App\Models\Cetak;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreCetakRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('cetak_create');
    }

    public function rules()
    {
        return [
            'no_spc' => [
                'string',
                'required',
                'unique:cetaks',
            ],
            'date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'semester_id' => [
                'required',
                'integer',
            ],
            'vendor_id' => [
                'required',
                'integer',
            ],
            'type' => [
                'required',
            ],
            'total_cost' => [
                'required',
            ],
            'total_oplah' => [
                'numeric',
                'required',
            ],
        ];
    }
}
