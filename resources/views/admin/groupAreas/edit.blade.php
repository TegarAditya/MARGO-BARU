@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.groupArea.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.group-areas.update", [$groupArea->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label for="code">{{ trans('cruds.groupArea.fields.code') }}</label>
                <input class="form-control {{ $errors->has('code') ? 'is-invalid' : '' }}" type="text" name="code" id="code" value="{{ old('code', $groupArea->code) }}">
                @if($errors->has('code'))
                    <span class="text-danger">{{ $errors->first('code') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.groupArea.fields.code_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.groupArea.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', $groupArea->name) }}" required>
                @if($errors->has('name'))
                    <span class="text-danger">{{ $errors->first('name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.groupArea.fields.name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.groupArea.fields.provinsi') }}</label>
                <select class="form-control {{ $errors->has('provinsi') ? 'is-invalid' : '' }}" name="provinsi" id="provinsi" required>
                    <option value disabled {{ old('provinsi', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\GroupArea::PROVINSI_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('provinsi', $groupArea->provinsi) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('provinsi'))
                    <span class="text-danger">{{ $errors->first('provinsi') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.groupArea.fields.provinsi_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="vendors">Marketing Area</label>
                <div style="padding-bottom: 4px">
                    <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                    <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                </div>
                <select class="form-control select2 {{ $errors->has('marketing_areas') ? 'is-invalid' : '' }}" name="marketing_areas[]" id="marketing_areas" multiple>
                    @foreach($marketing_areas as $id => $marketing_area)
                        <option value="{{ $id }}" {{ (in_array($id, old('marketing_areas', [])) || $groupArea->marketing_areas->contains($id)) ? 'selected' : '' }}>{{ $marketing_area }}</option>
                    @endforeach
                </select>
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
