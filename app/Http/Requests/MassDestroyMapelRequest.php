<?php

namespace App\Http\Requests;

use App\Models\Mapel;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyMapelRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('mapel_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:mapels,id',
        ];
    }
}
