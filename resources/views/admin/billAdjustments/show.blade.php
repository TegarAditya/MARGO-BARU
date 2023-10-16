@extends('layouts.admin')
@section('content')

<div class="row mb-4">
    <div class="col-12">
        <h1 class="m-0 bold">Billing Adjustment</h1>
    </div>
</div>
<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.billAdjustment.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.bill-adjustments.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.billAdjustment.fields.no_adjustment') }}
                        </th>
                        <td>
                            {{ $billAdjustment->no_adjustment }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.billAdjustment.fields.date') }}
                        </th>
                        <td>
                            {{ $billAdjustment->date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.billAdjustment.fields.salesperson') }}
                        </th>
                        <td>
                            {{ $billAdjustment->salesperson->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.billAdjustment.fields.semester') }}
                        </th>
                        <td>
                            {{ $billAdjustment->semester->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.billAdjustment.fields.amount') }}
                        </th>
                        <td>
                            {{ money($billAdjustment->amount) }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.billAdjustment.fields.note') }}
                        </th>
                        <td>
                            {{ $billAdjustment->note }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Dibuat Oleh
                        </th>
                        <td>
                            {{ $billAdjustment->created_by->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Diedit Oleh
                        </th>
                        <td>
                            {{ $billAdjustment->updated_by->name }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.bill-adjustments.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection
