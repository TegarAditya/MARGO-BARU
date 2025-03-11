<?php

namespace App\Http\Requests;

use App\Models\FinishingItem;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreFinishingItemRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('finishing_item_create');
    }

    public function rules()
    {
        return [
            'semester_id' => [
                'required',
                'integer',
            ],
            'buku_id' => [
                'required',
                'integer',
            ],
            'product_id' => [
                'required',
                'integer',
            ],
            'quantity' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'cost' => [
                'required',
            ],
        ];
    }
}
