<?php

namespace App\Http\Requests;

use App\Models\CetakItem;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreCetakItemRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('cetak_item_create');
    }

    public function rules()
    {
        return [
            'semester_id' => [
                'required',
                'integer',
            ],
            'product_id' => [
                'required',
                'integer',
            ],
            'halaman_id' => [
                'required',
                'integer',
            ],
            'quantity' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'cost' => [
                'required',
            ],
            'plate_cost' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'paper_cost' => [
                'numeric',
            ],
        ];
    }
}
