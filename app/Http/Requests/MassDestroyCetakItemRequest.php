<?php

namespace App\Http\Requests;

use App\Models\CetakItem;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyCetakItemRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('cetak_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:cetak_items,id',
        ];
    }
}
