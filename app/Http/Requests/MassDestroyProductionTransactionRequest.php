<?php

namespace App\Http\Requests;

use App\Models\ProductionTransaction;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyProductionTransactionRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('production_transaction_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:production_transactions,id',
        ];
    }
}
