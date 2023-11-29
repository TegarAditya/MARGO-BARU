@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.stockSaldo.title_singular') }}
    </div>

    <div class="card-body">
        <form class="form-prevent-multiple-submits" method="POST" action="{{ route("admin.stock-saldos.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="product_id">{{ trans('cruds.stockSaldo.fields.product') }}</label>
                <select class="form-control select2 {{ $errors->has('product') ? 'is-invalid' : '' }}" name="product_id" id="product_id">
                    @foreach($products as $id => $entry)
                        <option value="{{ $id }}" {{ old('product_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('product'))
                    <span class="text-danger">{{ $errors->first('product') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.stockSaldo.fields.product_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="material_id">{{ trans('cruds.stockSaldo.fields.material') }}</label>
                <select class="form-control select2 {{ $errors->has('material') ? 'is-invalid' : '' }}" name="material_id" id="material_id">
                    @foreach($materials as $id => $entry)
                        <option value="{{ $id }}" {{ old('material_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('material'))
                    <span class="text-danger">{{ $errors->first('material') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.stockSaldo.fields.material_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="periode">{{ trans('cruds.stockSaldo.fields.periode') }}</label>
                <input class="form-control {{ $errors->has('periode') ? 'is-invalid' : '' }}" type="text" name="periode" id="periode" value="{{ old('periode', '') }}" required>
                @if($errors->has('periode'))
                    <span class="text-danger">{{ $errors->first('periode') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.stockSaldo.fields.periode_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="start_date">{{ trans('cruds.stockSaldo.fields.start_date') }}</label>
                <input class="form-control date {{ $errors->has('start_date') ? 'is-invalid' : '' }}" type="text" name="start_date" id="start_date" value="{{ old('start_date') }}" required>
                @if($errors->has('start_date'))
                    <span class="text-danger">{{ $errors->first('start_date') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.stockSaldo.fields.start_date_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="end_date">{{ trans('cruds.stockSaldo.fields.end_date') }}</label>
                <input class="form-control date {{ $errors->has('end_date') ? 'is-invalid' : '' }}" type="text" name="end_date" id="end_date" value="{{ old('end_date') }}" required>
                @if($errors->has('end_date'))
                    <span class="text-danger">{{ $errors->first('end_date') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.stockSaldo.fields.end_date_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="qty_awal">{{ trans('cruds.stockSaldo.fields.qty_awal') }}</label>
                <input class="form-control {{ $errors->has('qty_awal') ? 'is-invalid' : '' }}" type="number" name="qty_awal" id="qty_awal" value="{{ old('qty_awal', '') }}" step="0.01" required>
                @if($errors->has('qty_awal'))
                    <span class="text-danger">{{ $errors->first('qty_awal') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.stockSaldo.fields.qty_awal_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="in">{{ trans('cruds.stockSaldo.fields.in') }}</label>
                <input class="form-control {{ $errors->has('in') ? 'is-invalid' : '' }}" type="number" name="in" id="in" value="{{ old('in', '') }}" step="0.01" required>
                @if($errors->has('in'))
                    <span class="text-danger">{{ $errors->first('in') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.stockSaldo.fields.in_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="out">{{ trans('cruds.stockSaldo.fields.out') }}</label>
                <input class="form-control {{ $errors->has('out') ? 'is-invalid' : '' }}" type="number" name="out" id="out" value="{{ old('out', '') }}" step="0.01" required>
                @if($errors->has('out'))
                    <span class="text-danger">{{ $errors->first('out') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.stockSaldo.fields.out_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="qty_akhir">{{ trans('cruds.stockSaldo.fields.qty_akhir') }}</label>
                <input class="form-control {{ $errors->has('qty_akhir') ? 'is-invalid' : '' }}" type="number" name="qty_akhir" id="qty_akhir" value="{{ old('qty_akhir', '') }}" step="0.01" required>
                @if($errors->has('qty_akhir'))
                    <span class="text-danger">{{ $errors->first('qty_akhir') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.stockSaldo.fields.qty_akhir_helper') }}</span>
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