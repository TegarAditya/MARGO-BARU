<?php

namespace App\Http\Requests;

use App\Models\TransactionTotal;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyTransactionTotalRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('transaction_total_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:transaction_totals,id',
        ];
    }
}
