<?php

namespace App\Http\Requests;

use App\Models\BookComponent;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyBookComponentRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('book_component_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:book_components,id',
        ];
    }
}
