<?php

namespace App\Http\Requests;

use App\Models\FinishingItem;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyFinishingItemRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('finishing_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:finishing_items,id',
        ];
    }
}
