<?php

namespace App\Http\Requests;

use App\Models\Finishing;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreFinishingRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('finishing_create');
    }

    public function rules()
    {
        return [
            'no_spk' => [
                'string',
                'required',
            ],
            'date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'semester_id' => [
                'required',
                'integer',
            ],
            'vendor_id' => [
                'required',
                'integer',
            ],
            'total_cost' => [
                'required',
            ],
            'total_oplah' => [
                'string',
                'required',
            ],
        ];
    }
}
