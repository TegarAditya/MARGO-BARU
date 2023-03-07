<?php

namespace App\Http\Requests;

use App\Models\ProductionEstimation;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyProductionEstimationRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('production_estimation_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:production_estimations,id',
        ];
    }
}
