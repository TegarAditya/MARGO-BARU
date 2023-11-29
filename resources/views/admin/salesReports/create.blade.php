@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.salesReport.title_singular') }}
    </div>

    <div class="card-body">
        <form class="form-prevent-multiple-submits" method="POST" action="{{ route("admin.sales-reports.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="code">{{ trans('cruds.salesReport.fields.code') }}</label>
                <input class="form-control {{ $errors->has('code') ? 'is-invalid' : '' }}" type="text" name="code" id="code" value="{{ old('code', '') }}" required>
                @if($errors->has('code'))
                    <span class="text-danger">{{ $errors->first('code') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.salesReport.fields.code_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="periode">{{ trans('cruds.salesReport.fields.periode') }}</label>
                <input class="form-control {{ $errors->has('periode') ? 'is-invalid' : '' }}" type="text" name="periode" id="periode" value="{{ old('periode', '') }}" required>
                @if($errors->has('periode'))
                    <span class="text-danger">{{ $errors->first('periode') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.salesReport.fields.periode_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="salesperson_id">{{ trans('cruds.salesReport.fields.salesperson') }}</label>
                <select class="form-control select2 {{ $errors->has('salesperson') ? 'is-invalid' : '' }}" name="salesperson_id" id="salesperson_id" required>
                    @foreach($salespeople as $id => $entry)
                        <option value="{{ $id }}" {{ old('salesperson_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('salesperson'))
                    <span class="text-danger">{{ $errors->first('salesperson') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.salesReport.fields.salesperson_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="start_date">{{ trans('cruds.salesReport.fields.start_date') }}</label>
                <input class="form-control date {{ $errors->has('start_date') ? 'is-invalid' : '' }}" type="text" name="start_date" id="start_date" value="{{ old('start_date') }}" required>
                @if($errors->has('start_date'))
                    <span class="text-danger">{{ $errors->first('start_date') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.salesReport.fields.start_date_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="end_date">{{ trans('cruds.salesReport.fields.end_date') }}</label>
                <input class="form-control date {{ $errors->has('end_date') ? 'is-invalid' : '' }}" type="text" name="end_date" id="end_date" value="{{ old('end_date') }}" required>
                @if($errors->has('end_date'))
                    <span class="text-danger">{{ $errors->first('end_date') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.salesReport.fields.end_date_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.salesReport.fields.type') }}</label>
                <select class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type" id="type">
                    <option value disabled {{ old('type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\SalesReport::TYPE_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('type', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('type'))
                    <span class="text-danger">{{ $errors->first('type') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.salesReport.fields.type_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="saldo_awal">{{ trans('cruds.salesReport.fields.saldo_awal') }}</label>
                <input class="form-control {{ $errors->has('saldo_awal') ? 'is-invalid' : '' }}" type="number" name="saldo_awal" id="saldo_awal" value="{{ old('saldo_awal', '') }}" step="0.01" required>
                @if($errors->has('saldo_awal'))
                    <span class="text-danger">{{ $errors->first('saldo_awal') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.salesReport.fields.saldo_awal_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="debet">{{ trans('cruds.salesReport.fields.debet') }}</label>
                <input class="form-control {{ $errors->has('debet') ? 'is-invalid' : '' }}" type="number" name="debet" id="debet" value="{{ old('debet', '0') }}" step="0.01" required>
                @if($errors->has('debet'))
                    <span class="text-danger">{{ $errors->first('debet') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.salesReport.fields.debet_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="kredit">{{ trans('cruds.salesReport.fields.kredit') }}</label>
                <input class="form-control {{ $errors->has('kredit') ? 'is-invalid' : '' }}" type="number" name="kredit" id="kredit" value="{{ old('kredit', '0') }}" step="0.01" required>
                @if($errors->has('kredit'))
                    <span class="text-danger">{{ $errors->first('kredit') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.salesReport.fields.kredit_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="saldo_akhir">{{ trans('cruds.salesReport.fields.saldo_akhir') }}</label>
                <input class="form-control {{ $errors->has('saldo_akhir') ? 'is-invalid' : '' }}" type="number" name="saldo_akhir" id="saldo_akhir" value="{{ old('saldo_akhir', '') }}" step="0.01" required>
                @if($errors->has('saldo_akhir'))
                    <span class="text-danger">{{ $errors->first('saldo_akhir') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.salesReport.fields.saldo_akhir_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger form-prevent-multiple-submits" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection