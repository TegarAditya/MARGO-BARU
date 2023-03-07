@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.returnGood.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.return-goods.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.returnGood.fields.no_retur') }}
                        </th>
                        <td>
                            {{ $returnGood->no_retur }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.returnGood.fields.date') }}
                        </th>
                        <td>
                            {{ $returnGood->date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.returnGood.fields.salesperson') }}
                        </th>
                        <td>
                            {{ $returnGood->salesperson->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.returnGood.fields.semester') }}
                        </th>
                        <td>
                            {{ $returnGood->semester->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.returnGood.fields.nominal') }}
                        </th>
                        <td>
                            {{ $returnGood->nominal }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.return-goods.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection