<?php

namespace App\Http\Requests;

use App\Models\VendorCost;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateVendorCostRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('vendor_cost_edit');
    }

    public function rules()
    {
        return [
            'vendor_id' => [
                'required',
                'integer',
            ],
            'key' => [
                'string',
                'required',
            ],
            'value' => [
                'required',
            ],
        ];
    }
}
