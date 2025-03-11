<?php

namespace App\Http\Requests;

use App\Models\Jenjang;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyJenjangRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('jenjang_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:jenjangs,id',
        ];
    }
}
