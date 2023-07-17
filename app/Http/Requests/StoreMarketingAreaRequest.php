<?php

namespace App\Http\Requests;

use App\Models\MarketingArea;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreMarketingAreaRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('marketing_area_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
                'unique:marketing_areas',
            ],
            'group_area_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
