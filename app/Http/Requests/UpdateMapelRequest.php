<?php

namespace App\Http\Requests;

use App\Models\Mapel;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateMapelRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('mapel_edit');
    }

    public function rules()
    {
        return [
            'code' => [
                'string',
                'required',
                'unique:mapels,code,' . request()->route('mapel')->id,
            ],
            'name' => [
                'string',
                'required',
            ],
        ];
    }
}
