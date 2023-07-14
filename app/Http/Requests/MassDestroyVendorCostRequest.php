<?php

namespace App\Http\Requests;

use App\Models\VendorCost;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyVendorCostRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('vendor_cost_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:vendor_costs,id',
        ];
    }
}
