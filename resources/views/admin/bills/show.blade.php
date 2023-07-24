@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.bill.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.bills.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.bill.fields.code') }}
                        </th>
                        <td>
                            {{ $bill->code }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bill.fields.semester') }}
                        </th>
                        <td>
                            {{ $bill->semester->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bill.fields.salesperson') }}
                        </th>
                        <td>
                            {{ $bill->salesperson->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bill.fields.start_date') }}
                        </th>
                        <td>
                            {{ $bill->start_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bill.fields.end_date') }}
                        </th>
                        <td>
                            {{ $bill->end_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bill.fields.saldo_awal') }}
                        </th>
                        <td>
                            {{ $bill->saldo_awal }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bill.fields.jual') }}
                        </th>
                        <td>
                            {{ $bill->jual }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bill.fields.diskon') }}
                        </th>
                        <td>
                            {{ $bill->diskon }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bill.fields.retur') }}
                        </th>
                        <td>
                            {{ $bill->retur }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bill.fields.bayar') }}
                        </th>
                        <td>
                            {{ $bill->bayar }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bill.fields.potongan') }}
                        </th>
                        <td>
                            {{ $bill->potongan }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bill.fields.saldo_akhir') }}
                        </th>
                        <td>
                            {{ $bill->saldo_akhir }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.bills.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection