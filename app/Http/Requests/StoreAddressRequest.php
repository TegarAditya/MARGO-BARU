<?php

namespace App\Http\Requests;

use App\Models\Address;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreAddressRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('address_create');
    }

    public function rules()
    {
        return [
            'salesperson_id' => [
                'required',
                'integer',
            ],
            'address' => [
                'required',
            ],
        ];
    }
}
