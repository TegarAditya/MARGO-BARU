<?php

namespace App\Http\Requests;

use App\Models\GroupArea;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyGroupAreaRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('group_area_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:group_areas,id',
        ];
    }
}
