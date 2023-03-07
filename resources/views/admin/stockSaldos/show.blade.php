@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.stockSaldo.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.stock-saldos.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.stockSaldo.fields.code') }}
                        </th>
                        <td>
                            {{ $stockSaldo->code }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.stockSaldo.fields.product') }}
                        </th>
                        <td>
                            {{ $stockSaldo->product->code ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.stockSaldo.fields.material') }}
                        </th>
                        <td>
                            {{ $stockSaldo->material->code ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.stockSaldo.fields.periode') }}
                        </th>
                        <td>
                            {{ $stockSaldo->periode }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.stockSaldo.fields.start_date') }}
                        </th>
                        <td>
                            {{ $stockSaldo->start_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.stockSaldo.fields.end_date') }}
                        </th>
                        <td>
                            {{ $stockSaldo->end_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.stockSaldo.fields.qty_awal') }}
                        </th>
                        <td>
                            {{ $stockSaldo->qty_awal }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.stockSaldo.fields.in') }}
                        </th>
                        <td>
                            {{ $stockSaldo->in }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.stockSaldo.fields.out') }}
                        </th>
                        <td>
                            {{ $stockSaldo->out }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.stockSaldo.fields.qty_akhir') }}
                        </th>
                        <td>
                            {{ $stockSaldo->qty_akhir }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.stock-saldos.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection