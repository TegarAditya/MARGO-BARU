<?php

namespace App\Http\Requests;

use App\Models\Kelas;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateKelaRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('kela_edit');
    }

    public function rules()
    {
        return [
            'code' => [
                'string',
                'required',
                'unique:kelas,code,' . request()->route('kela')->id,
            ],
            'name' => [
                'string',
                'required',
            ],
        ];
    }
}
