<?php

namespace App\Http\Requests;

use App\Models\Warehouse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreWarehouseRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('warehouse_create');
    }

    public function rules()
    {
        return [
            'code' => [
                'string',
                'required',
                'unique:warehouses',
            ],
            'name' => [
                'string',
                'required',
                'unique:warehouses',
            ],
        ];
    }
}
