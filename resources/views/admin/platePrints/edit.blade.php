@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.platePrint.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.plate-prints.update", [$platePrint->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="no_spk">{{ trans('cruds.platePrint.fields.no_spk') }}</label>
                <input class="form-control {{ $errors->has('no_spk') ? 'is-invalid' : '' }}" type="text" name="no_spk" id="no_spk" value="{{ old('no_spk', $platePrint->no_spk) }}" required>
                @if($errors->has('no_spk'))
                    <span class="text-danger">{{ $errors->first('no_spk') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.platePrint.fields.no_spk_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="date">{{ trans('cruds.platePrint.fields.date') }}</label>
                <input class="form-control date {{ $errors->has('date') ? 'is-invalid' : '' }}" type="text" name="date" id="date" value="{{ old('date', $platePrint->date) }}" required>
                @if($errors->has('date'))
                    <span class="text-danger">{{ $errors->first('date') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.platePrint.fields.date_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="semester_id">{{ trans('cruds.platePrint.fields.semester') }}</label>
                <select class="form-control select2 {{ $errors->has('semester') ? 'is-invalid' : '' }}" name="semester_id" id="semester_id" required>
                    @foreach($semesters as $id => $entry)
                        <option value="{{ $id }}" {{ (old('semester_id') ? old('semester_id') : $platePrint->semester->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('semester'))
                    <span class="text-danger">{{ $errors->first('semester') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.platePrint.fields.semester_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="vendor_id">{{ trans('cruds.platePrint.fields.vendor') }}</label>
                <select class="form-control select2 {{ $errors->has('vendor') ? 'is-invalid' : '' }}" name="vendor_id" id="vendor_id" required>
                    @foreach($vendors as $id => $entry)
                        <option value="{{ $id }}" {{ (old('vendor_id') ? old('vendor_id') : $platePrint->vendor->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('vendor'))
                    <span class="text-danger">{{ $errors->first('vendor') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.platePrint.fields.vendor_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="note">{{ trans('cruds.platePrint.fields.note') }}</label>
                <textarea class="form-control {{ $errors->has('note') ? 'is-invalid' : '' }}" name="note" id="note">{{ old('note', $platePrint->note) }}</textarea>
                @if($errors->has('note'))
                    <span class="text-danger">{{ $errors->first('note') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.platePrint.fields.note_helper') }}</span>
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