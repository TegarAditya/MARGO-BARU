<?php

namespace App\Http\Requests;

use App\Models\BillAdjustment;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyBillAdjustmentRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('bill_adjustment_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:bill_adjustments,id',
        ];
    }
}
