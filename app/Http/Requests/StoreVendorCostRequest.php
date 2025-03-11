<?php

namespace App\Http\Requests;

use App\Models\VendorCost;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreVendorCostRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('vendor_cost_create');
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
