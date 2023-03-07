<?php

namespace App\Http\Requests;

use App\Models\MarketingArea;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyMarketingAreaRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('marketing_area_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:marketing_areas,id',
        ];
    }
}
