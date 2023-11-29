@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.setting.title_singular') }}
    </div>

    <div class="card-body">
        <form class="form-prevent-multiple-submits" method="POST" action="{{ route("admin.settings.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="key">{{ trans('cruds.setting.fields.key') }}</label>
                <input class="form-control {{ $errors->has('key') ? 'is-invalid' : '' }}" type="text" name="key" id="key" value="{{ old('key', '') }}" required>
                @if($errors->has('key'))
                    <span class="text-danger">{{ $errors->first('key') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.setting.fields.key_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="value">{{ trans('cruds.setting.fields.value') }}</label>
                <textarea class="form-control {{ $errors->has('value') ? 'is-invalid' : '' }}" name="value" id="value" required>{{ old('value') }}</textarea>
                @if($errors->has('value'))
                    <span class="text-danger">{{ $errors->first('value') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.setting.fields.value_helper') }}</span>
            </div>
            <div class="form-group">
                <div class="form-check {{ $errors->has('is_json') ? 'is-invalid' : '' }}">
                    <input type="hidden" name="is_json" value="0">
                    <input class="form-check-input" type="checkbox" name="is_json" id="is_json" value="1" {{ old('is_json', 0) == 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_json">{{ trans('cruds.setting.fields.is_json') }}</label>
                </div>
                @if($errors->has('is_json'))
                    <span class="text-danger">{{ $errors->first('is_json') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.setting.fields.is_json_helper') }}</span>
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