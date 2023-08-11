<?php

namespace App\Http\Requests;

use App\Models\DeliveryPlate;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreDeliveryPlateRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('delivery_plate_create');
    }

    public function rules()
    {
        return [
            'no_suratjalan' => [
                'string',
                'required',
                'unique:delivery_plates',
            ],
            'date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'customer' => [
                'string',
                'nullable',
            ],
        ];
    }
}
