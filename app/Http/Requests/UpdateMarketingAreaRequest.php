<?php

namespace App\Http\Requests;

use App\Models\MarketingArea;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateMarketingAreaRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('marketing_area_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
                'unique:marketing_areas,name,' . request()->route('marketing_area')->id,
            ],
        ];
    }
}
