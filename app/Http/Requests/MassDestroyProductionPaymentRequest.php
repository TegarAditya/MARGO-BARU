<?php

namespace App\Http\Requests;

use App\Models\ProductionPayment;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyProductionPaymentRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('production_payment_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:production_payments,id',
        ];
    }
}
