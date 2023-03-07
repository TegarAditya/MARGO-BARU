<?php

namespace App\Http\Requests;

use App\Models\ReturnGood;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyReturnGoodRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('return_good_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:return_goods,id',
        ];
    }
}
