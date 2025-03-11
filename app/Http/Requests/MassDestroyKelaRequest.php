<?php

namespace App\Http\Requests;

use App\Models\Kelas;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyKelaRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('kela_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:kelas,id',
        ];
    }
}
