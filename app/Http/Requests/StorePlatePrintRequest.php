<?php

namespace App\Http\Requests;

use App\Models\PlatePrint;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StorePlatePrintRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('plate_print_create');
    }

    public function rules()
    {
        return [
            'no_spk' => [
                'string',
                'required',
                'unique:plate_prints',
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
        ];
    }
}
