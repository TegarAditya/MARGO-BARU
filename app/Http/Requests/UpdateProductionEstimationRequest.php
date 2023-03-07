<?php

namespace App\Http\Requests;

use App\Models\ProductionEstimation;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateProductionEstimationRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('production_estimation_edit');
    }

    public function rules()
    {
        return [
            'product_id' => [
                'required',
                'integer',
            ],
            'quantity' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'estimasi' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'isi' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'cover' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'finishing' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
        ];
    }
}
