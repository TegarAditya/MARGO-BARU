<?php

namespace App\Http\Requests;

use App\Models\FinishingMasuk;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreFinishingMasukRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('finishing_masuk_create');
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
            'vendor_id' => [
                'required',
                'integer',
            ],
            'quantity' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
        ];
    }
}
