@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.salesReport.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.sales-reports.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.salesReport.fields.code') }}
                        </th>
                        <td>
                            {{ $salesReport->code }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.salesReport.fields.periode') }}
                        </th>
                        <td>
                            {{ $salesReport->periode }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.salesReport.fields.salesperson') }}
                        </th>
                        <td>
                            {{ $salesReport->salesperson->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.salesReport.fields.start_date') }}
                        </th>
                        <td>
                            {{ $salesReport->start_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.salesReport.fields.end_date') }}
                        </th>
                        <td>
                            {{ $salesReport->end_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.salesReport.fields.type') }}
                        </th>
                        <td>
                            {{ App\Models\SalesReport::TYPE_SELECT[$salesReport->type] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.salesReport.fields.saldo_awal') }}
                        </th>
                        <td>
                            {{ $salesReport->saldo_awal }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.salesReport.fields.debet') }}
                        </th>
                        <td>
                            {{ $salesReport->debet }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.salesReport.fields.kredit') }}
                        </th>
                        <td>
                            {{ $salesReport->kredit }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.salesReport.fields.saldo_akhir') }}
                        </th>
                        <td>
                            {{ $salesReport->saldo_akhir }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.sales-reports.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection