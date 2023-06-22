@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.estimationMovement.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.estimation-movements.update", [$estimationMovement->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="movement_date">{{ trans('cruds.estimationMovement.fields.movement_date') }}</label>
                <input class="form-control date {{ $errors->has('movement_date') ? 'is-invalid' : '' }}" type="text" name="movement_date" id="movement_date" value="{{ old('movement_date', $estimationMovement->movement_date) }}" required>
                @if($errors->has('movement_date'))
                    <span class="text-danger">{{ $errors->first('movement_date') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.estimationMovement.fields.movement_date_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.estimationMovement.fields.movement_type') }}</label>
                <select class="form-control {{ $errors->has('movement_type') ? 'is-invalid' : '' }}" name="movement_type" id="movement_type" required>
                    <option value disabled {{ old('movement_type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\EstimationMovement::MOVEMENT_TYPE_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('movement_type', $estimationMovement->movement_type) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('movement_type'))
                    <span class="text-danger">{{ $errors->first('movement_type') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.estimationMovement.fields.movement_type_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="product_id">{{ trans('cruds.estimationMovement.fields.product') }}</label>
                <select class="form-control select2 {{ $errors->has('product') ? 'is-invalid' : '' }}" name="product_id" id="product_id">
                    @foreach($products as $id => $entry)
                        <option value="{{ $id }}" {{ (old('product_id') ? old('product_id') : $estimationMovement->product->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('product'))
                    <span class="text-danger">{{ $errors->first('product') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.estimationMovement.fields.product_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.estimationMovement.fields.type') }}</label>
                <select class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type" id="type" required>
                    <option value disabled {{ old('type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\EstimationMovement::TYPE_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('type', $estimationMovement->type) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('type'))
                    <span class="text-danger">{{ $errors->first('type') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.estimationMovement.fields.type_helper') }}</span>
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