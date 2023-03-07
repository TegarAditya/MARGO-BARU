@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.cetak.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.cetaks.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.cetak.fields.no_spc') }}
                        </th>
                        <td>
                            {{ $cetak->no_spc }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.cetak.fields.date') }}
                        </th>
                        <td>
                            {{ $cetak->date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.cetak.fields.semester') }}
                        </th>
                        <td>
                            {{ $cetak->semester->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.cetak.fields.vendor') }}
                        </th>
                        <td>
                            {{ $cetak->vendor->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.cetak.fields.type') }}
                        </th>
                        <td>
                            {{ App\Models\Cetak::TYPE_SELECT[$cetak->type] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.cetak.fields.total_cost') }}
                        </th>
                        <td>
                            {{ $cetak->total_cost }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.cetak.fields.total_oplah') }}
                        </th>
                        <td>
                            {{ $cetak->total_oplah }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.cetak.fields.note') }}
                        </th>
                        <td>
                            {{ $cetak->note }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.cetaks.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection