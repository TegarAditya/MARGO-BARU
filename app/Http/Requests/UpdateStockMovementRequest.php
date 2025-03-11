<?php

namespace App\Http\Requests;

use App\Models\StockMovement;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateStockMovementRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('stock_movement_edit');
    }

    public function rules()
    {
        return [
            'movement_date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'movement_type' => [
                'required',
            ],
            'quantity' => [
                'numeric',
                'required',
            ],
        ];
    }
}
