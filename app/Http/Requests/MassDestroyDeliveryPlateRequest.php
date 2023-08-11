<?php

namespace App\Http\Requests;

use App\Models\DeliveryPlate;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyDeliveryPlateRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('delivery_plate_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:delivery_plates,id',
        ];
    }
}
