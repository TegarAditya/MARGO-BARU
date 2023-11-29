@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.bill.title_singular') }}
    </div>

    <div class="card-body">
        <form class="form-prevent-multiple-submits" method="POST" action="{{ route("admin.bills.update", [$bill->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="code">{{ trans('cruds.bill.fields.code') }}</label>
                <input class="form-control {{ $errors->has('code') ? 'is-invalid' : '' }}" type="text" name="code" id="code" value="{{ old('code', $bill->code) }}" required>
                @if($errors->has('code'))
                    <span class="text-danger">{{ $errors->first('code') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bill.fields.code_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="semester_id">{{ trans('cruds.bill.fields.semester') }}</label>
                <select class="form-control select2 {{ $errors->has('semester') ? 'is-invalid' : '' }}" name="semester_id" id="semester_id" required>
                    @foreach($semesters as $id => $entry)
                        <option value="{{ $id }}" {{ (old('semester_id') ? old('semester_id') : $bill->semester->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('semester'))
                    <span class="text-danger">{{ $errors->first('semester') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bill.fields.semester_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="salesperson_id">{{ trans('cruds.bill.fields.salesperson') }}</label>
                <select class="form-control select2 {{ $errors->has('salesperson') ? 'is-invalid' : '' }}" name="salesperson_id" id="salesperson_id" required>
                    @foreach($salespeople as $id => $entry)
                        <option value="{{ $id }}" {{ (old('salesperson_id') ? old('salesperson_id') : $bill->salesperson->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('salesperson'))
                    <span class="text-danger">{{ $errors->first('salesperson') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bill.fields.salesperson_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="start_date">{{ trans('cruds.bill.fields.start_date') }}</label>
                <input class="form-control date {{ $errors->has('start_date') ? 'is-invalid' : '' }}" type="text" name="start_date" id="start_date" value="{{ old('start_date', $bill->start_date) }}" required>
                @if($errors->has('start_date'))
                    <span class="text-danger">{{ $errors->first('start_date') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bill.fields.start_date_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="end_date">{{ trans('cruds.bill.fields.end_date') }}</label>
                <input class="form-control date {{ $errors->has('end_date') ? 'is-invalid' : '' }}" type="text" name="end_date" id="end_date" value="{{ old('end_date', $bill->end_date) }}" required>
                @if($errors->has('end_date'))
                    <span class="text-danger">{{ $errors->first('end_date') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bill.fields.end_date_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="saldo_awal">{{ trans('cruds.bill.fields.saldo_awal') }}</label>
                <input class="form-control {{ $errors->has('saldo_awal') ? 'is-invalid' : '' }}" type="number" name="saldo_awal" id="saldo_awal" value="{{ old('saldo_awal', $bill->saldo_awal) }}" step="0.01" required>
                @if($errors->has('saldo_awal'))
                    <span class="text-danger">{{ $errors->first('saldo_awal') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bill.fields.saldo_awal_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="jual">{{ trans('cruds.bill.fields.jual') }}</label>
                <input class="form-control {{ $errors->has('jual') ? 'is-invalid' : '' }}" type="number" name="jual" id="jual" value="{{ old('jual', $bill->jual) }}" step="0.01">
                @if($errors->has('jual'))
                    <span class="text-danger">{{ $errors->first('jual') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bill.fields.jual_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="diskon">{{ trans('cruds.bill.fields.diskon') }}</label>
                <input class="form-control {{ $errors->has('diskon') ? 'is-invalid' : '' }}" type="number" name="diskon" id="diskon" value="{{ old('diskon', $bill->diskon) }}" step="0.01">
                @if($errors->has('diskon'))
                    <span class="text-danger">{{ $errors->first('diskon') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bill.fields.diskon_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="retur">{{ trans('cruds.bill.fields.retur') }}</label>
                <input class="form-control {{ $errors->has('retur') ? 'is-invalid' : '' }}" type="number" name="retur" id="retur" value="{{ old('retur', $bill->retur) }}" step="0.01">
                @if($errors->has('retur'))
                    <span class="text-danger">{{ $errors->first('retur') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bill.fields.retur_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="bayar">{{ trans('cruds.bill.fields.bayar') }}</label>
                <input class="form-control {{ $errors->has('bayar') ? 'is-invalid' : '' }}" type="number" name="bayar" id="bayar" value="{{ old('bayar', $bill->bayar) }}" step="0.01">
                @if($errors->has('bayar'))
                    <span class="text-danger">{{ $errors->first('bayar') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bill.fields.bayar_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="potongan">{{ trans('cruds.bill.fields.potongan') }}</label>
                <input class="form-control {{ $errors->has('potongan') ? 'is-invalid' : '' }}" type="number" name="potongan" id="potongan" value="{{ old('potongan', $bill->potongan) }}" step="0.01">
                @if($errors->has('potongan'))
                    <span class="text-danger">{{ $errors->first('potongan') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bill.fields.potongan_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="saldo_akhir">{{ trans('cruds.bill.fields.saldo_akhir') }}</label>
                <input class="form-control {{ $errors->has('saldo_akhir') ? 'is-invalid' : '' }}" type="number" name="saldo_akhir" id="saldo_akhir" value="{{ old('saldo_akhir', $bill->saldo_akhir) }}" step="0.01" required>
                @if($errors->has('saldo_akhir'))
                    <span class="text-danger">{{ $errors->first('saldo_akhir') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bill.fields.saldo_akhir_helper') }}</span>
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