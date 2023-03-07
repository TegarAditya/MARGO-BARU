@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.cetak.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.cetaks.update", [$cetak->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="no_spc">{{ trans('cruds.cetak.fields.no_spc') }}</label>
                <input class="form-control {{ $errors->has('no_spc') ? 'is-invalid' : '' }}" type="text" name="no_spc" id="no_spc" value="{{ old('no_spc', $cetak->no_spc) }}" required>
                @if($errors->has('no_spc'))
                    <span class="text-danger">{{ $errors->first('no_spc') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.cetak.fields.no_spc_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="date">{{ trans('cruds.cetak.fields.date') }}</label>
                <input class="form-control date {{ $errors->has('date') ? 'is-invalid' : '' }}" type="text" name="date" id="date" value="{{ old('date', $cetak->date) }}" required>
                @if($errors->has('date'))
                    <span class="text-danger">{{ $errors->first('date') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.cetak.fields.date_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="semester_id">{{ trans('cruds.cetak.fields.semester') }}</label>
                <select class="form-control select2 {{ $errors->has('semester') ? 'is-invalid' : '' }}" name="semester_id" id="semester_id" required>
                    @foreach($semesters as $id => $entry)
                        <option value="{{ $id }}" {{ (old('semester_id') ? old('semester_id') : $cetak->semester->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('semester'))
                    <span class="text-danger">{{ $errors->first('semester') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.cetak.fields.semester_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="vendor_id">{{ trans('cruds.cetak.fields.vendor') }}</label>
                <select class="form-control select2 {{ $errors->has('vendor') ? 'is-invalid' : '' }}" name="vendor_id" id="vendor_id" required>
                    @foreach($vendors as $id => $entry)
                        <option value="{{ $id }}" {{ (old('vendor_id') ? old('vendor_id') : $cetak->vendor->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('vendor'))
                    <span class="text-danger">{{ $errors->first('vendor') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.cetak.fields.vendor_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.cetak.fields.type') }}</label>
                <select class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type" id="type" required>
                    <option value disabled {{ old('type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Cetak::TYPE_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('type', $cetak->type) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('type'))
                    <span class="text-danger">{{ $errors->first('type') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.cetak.fields.type_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="total_cost">{{ trans('cruds.cetak.fields.total_cost') }}</label>
                <input class="form-control {{ $errors->has('total_cost') ? 'is-invalid' : '' }}" type="number" name="total_cost" id="total_cost" value="{{ old('total_cost', $cetak->total_cost) }}" step="0.01" required>
                @if($errors->has('total_cost'))
                    <span class="text-danger">{{ $errors->first('total_cost') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.cetak.fields.total_cost_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="total_oplah">{{ trans('cruds.cetak.fields.total_oplah') }}</label>
                <input class="form-control {{ $errors->has('total_oplah') ? 'is-invalid' : '' }}" type="number" name="total_oplah" id="total_oplah" value="{{ old('total_oplah', $cetak->total_oplah) }}" step="0.01" required>
                @if($errors->has('total_oplah'))
                    <span class="text-danger">{{ $errors->first('total_oplah') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.cetak.fields.total_oplah_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="note">{{ trans('cruds.cetak.fields.note') }}</label>
                <textarea class="form-control {{ $errors->has('note') ? 'is-invalid' : '' }}" name="note" id="note">{{ old('note', $cetak->note) }}</textarea>
                @if($errors->has('note'))
                    <span class="text-danger">{{ $errors->first('note') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.cetak.fields.note_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection