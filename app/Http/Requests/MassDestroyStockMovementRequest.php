<?php

namespace App\Http\Requests;

use App\Models\StockMovement;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyStockMovementRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('stock_movement_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:stock_movements,id',
        ];
    }
}
