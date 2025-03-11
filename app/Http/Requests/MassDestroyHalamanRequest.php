<?php

namespace App\Http\Requests;

use App\Models\Halaman;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyHalamanRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('halaman_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:halamen,id',
        ];
    }
}
