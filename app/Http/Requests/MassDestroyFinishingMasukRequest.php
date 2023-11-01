<?php

namespace App\Http\Requests;

use App\Models\FinishingMasuk;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyFinishingMasukRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('finishing_masuk_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:finishing_masuks,id',
        ];
    }
}
