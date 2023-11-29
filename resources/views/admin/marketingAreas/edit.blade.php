@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.marketingArea.title_singular') }}
    </div>

    <div class="card-body">
        <form class="form-prevent-multiple-submits" method="POST" action="{{ route("admin.marketing-areas.update", [$marketingArea->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.marketingArea.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', $marketingArea->name) }}" required>
                @if($errors->has('name'))
                    <span class="text-danger">{{ $errors->first('name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.marketingArea.fields.name_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="description">{{ trans('cruds.marketingArea.fields.description') }}</label>
                <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description" id="description">{{ old('description', $marketingArea->description) }}</textarea>
                @if($errors->has('description'))
                    <span class="text-danger">{{ $errors->first('description') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.marketingArea.fields.description_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="group_area_id">{{ trans('cruds.marketingArea.fields.group_area') }}</label>
                <select class="form-control select2 {{ $errors->has('group_area') ? 'is-invalid' : '' }}" name="group_area_id" id="group_area_id" required>
                    @foreach($group_areas as $id => $entry)
                        <option value="{{ $id }}" {{ (old('group_area_id') ? old('group_area_id') : $marketingArea->group_area->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('group_area'))
                    <span class="text-danger">{{ $errors->first('group_area') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.marketingArea.fields.group_area_helper') }}</span>
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