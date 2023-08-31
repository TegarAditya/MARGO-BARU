<?php

namespace App\Http\Requests;

use App\Models\Warehouse;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateWarehouseRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('warehouse_edit');
    }

    public function rules()
    {
        return [
            'code' => [
                'string',
                'unique:warehouses,code,' . request()->route('warehouse')->id,
            ],
            'name' => [
                'string',
                'required',
                'unique:warehouses,name,' . request()->route('warehouse')->id,
            ],
        ];
    }
}
