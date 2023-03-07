<?php

namespace App\Http\Requests;

use App\Models\SalesReport;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateSalesReportRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('sales_report_edit');
    }

    public function rules()
    {
        return [
            'code' => [
                'string',
                'required',
                'unique:sales_reports,code,' . request()->route('sales_report')->id,
            ],
            'periode' => [
                'string',
                'required',
            ],
            'salesperson_id' => [
                'required',
                'integer',
            ],
            'start_date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'end_date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'saldo_awal' => [
                'required',
            ],
            'debet' => [
                'required',
            ],
            'kredit' => [
                'required',
            ],
            'saldo_akhir' => [
                'required',
            ],
        ];
    }
}
