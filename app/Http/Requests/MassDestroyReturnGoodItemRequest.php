<?php

namespace App\Http\Requests;

use App\Models\ReturnGoodItem;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyReturnGoodItemRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('return_good_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:return_good_items,id',
        ];
    }
}
