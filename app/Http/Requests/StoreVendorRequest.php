<?php

namespace App\Http\Requests;

use App\Models\Vendor;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreVendorRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('vendor_create');
    }

    public function rules()
    {
        return [
            'code' => [
                'string',
                'required',
                'unique:vendors',
            ],
            'name' => [
                'string',
                'required',
            ],
            'type' => [
                'required',
            ],
            'contact' => [
                'string',
                'nullable',
            ],
            'company' => [
                'string',
                'nullable',
            ],
        ];
    }
}
