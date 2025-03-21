<?php

namespace App\Http\Requests;

use App\Models\Unit;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateUnitRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('unit_edit');
    }

    public function rules()
    {
        return [
            'code' => [
                'string',
                'required',
                'unique:units,code,' . request()->route('unit')->id,
            ],
            'name' => [
                'string',
                'required',
            ],
        ];
    }
}
