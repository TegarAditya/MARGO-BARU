@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.transactionTotal.title_singular') }}
    </div>

    <div class="card-body">
        <form class="form-prevent-multiple-submits" method="POST" action="{{ route("admin.transaction-totals.update", [$transactionTotal->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="salesperson_id">{{ trans('cruds.transactionTotal.fields.salesperson') }}</label>
                <select class="form-control select2 {{ $errors->has('salesperson') ? 'is-invalid' : '' }}" name="salesperson_id" id="salesperson_id" required>
                    @foreach($salespeople as $id => $entry)
                        <option value="{{ $id }}" {{ (old('salesperson_id') ? old('salesperson_id') : $transactionTotal->salesperson->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('salesperson'))
                    <span class="text-danger">{{ $errors->first('salesperson') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.transactionTotal.fields.salesperson_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="total_invoice">{{ trans('cruds.transactionTotal.fields.total_invoice') }}</label>
                <input class="form-control {{ $errors->has('total_invoice') ? 'is-invalid' : '' }}" type="number" name="total_invoice" id="total_invoice" value="{{ old('total_invoice', $transactionTotal->total_invoice) }}" step="0.01" required>
                @if($errors->has('total_invoice'))
                    <span class="text-danger">{{ $errors->first('total_invoice') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.transactionTotal.fields.total_invoice_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="total_diskon">{{ trans('cruds.transactionTotal.fields.total_diskon') }}</label>
                <input class="form-control {{ $errors->has('total_diskon') ? 'is-invalid' : '' }}" type="number" name="total_diskon" id="total_diskon" value="{{ old('total_diskon', $transactionTotal->total_diskon) }}" step="0.01" required>
                @if($errors->has('total_diskon'))
                    <span class="text-danger">{{ $errors->first('total_diskon') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.transactionTotal.fields.total_diskon_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="total_retur">{{ trans('cruds.transactionTotal.fields.total_retur') }}</label>
                <input class="form-control {{ $errors->has('total_retur') ? 'is-invalid' : '' }}" type="number" name="total_retur" id="total_retur" value="{{ old('total_retur', $transactionTotal->total_retur) }}" step="0.01" required>
                @if($errors->has('total_retur'))
                    <span class="text-danger">{{ $errors->first('total_retur') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.transactionTotal.fields.total_retur_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="total_bayar">{{ trans('cruds.transactionTotal.fields.total_bayar') }}</label>
                <input class="form-control {{ $errors->has('total_bayar') ? 'is-invalid' : '' }}" type="number" name="total_bayar" id="total_bayar" value="{{ old('total_bayar', $transactionTotal->total_bayar) }}" step="0.01" required>
                @if($errors->has('total_bayar'))
                    <span class="text-danger">{{ $errors->first('total_bayar') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.transactionTotal.fields.total_bayar_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="total_potongan">{{ trans('cruds.transactionTotal.fields.total_potongan') }}</label>
                <input class="form-control {{ $errors->has('total_potongan') ? 'is-invalid' : '' }}" type="number" name="total_potongan" id="total_potongan" value="{{ old('total_potongan', $transactionTotal->total_potongan) }}" step="0.01" required>
                @if($errors->has('total_potongan'))
                    <span class="text-danger">{{ $errors->first('total_potongan') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.transactionTotal.fields.total_potongan_helper') }}</span>
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