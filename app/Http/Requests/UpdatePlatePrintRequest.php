<?php

namespace App\Http\Requests;

use App\Models\PlatePrint;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdatePlatePrintRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('plate_print_edit');
    }

    public function rules()
    {
        return [
            'no_spk' => [
                'string',
                'required',
                'unique:plate_prints,no_spk,' . request()->route('plate_print')->id,
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
