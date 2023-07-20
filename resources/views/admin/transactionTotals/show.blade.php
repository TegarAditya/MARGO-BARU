@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.transactionTotal.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.transaction-totals.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.transactionTotal.fields.salesperson') }}
                        </th>
                        <td>
                            {{ $transactionTotal->salesperson->code ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.transactionTotal.fields.total_invoice') }}
                        </th>
                        <td>
                            {{ $transactionTotal->total_invoice }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.transactionTotal.fields.total_diskon') }}
                        </th>
                        <td>
                            {{ $transactionTotal->total_diskon }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.transactionTotal.fields.total_retur') }}
                        </th>
                        <td>
                            {{ $transactionTotal->total_retur }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.transactionTotal.fields.total_bayar') }}
                        </th>
                        <td>
                            {{ $transactionTotal->total_bayar }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.transactionTotal.fields.total_potongan') }}
                        </th>
                        <td>
                            {{ $transactionTotal->total_potongan }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.transaction-totals.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection