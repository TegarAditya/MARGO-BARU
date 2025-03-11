<?php

namespace App\Http\Requests;

use App\Models\Cetak;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyCetakRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('cetak_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:cetaks,id',
        ];
    }
}
