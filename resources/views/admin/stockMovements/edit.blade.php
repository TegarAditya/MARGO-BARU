@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.stockMovement.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.stock-movements.update", [$stockMovement->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="movement_date">{{ trans('cruds.stockMovement.fields.movement_date') }}</label>
                <input class="form-control date {{ $errors->has('movement_date') ? 'is-invalid' : '' }}" type="text" name="movement_date" id="movement_date" value="{{ old('movement_date', $stockMovement->movement_date) }}" required>
                @if($errors->has('movement_date'))
                    <span class="text-danger">{{ $errors->first('movement_date') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.stockMovement.fields.movement_date_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.stockMovement.fields.movement_type') }}</label>
                <select class="form-control {{ $errors->has('movement_type') ? 'is-invalid' : '' }}" name="movement_type" id="movement_type" required>
                    <option value disabled {{ old('movement_type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\StockMovement::MOVEMENT_TYPE_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('movement_type', $stockMovement->movement_type) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('movement_type'))
                    <span class="text-danger">{{ $errors->first('movement_type') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.stockMovement.fields.movement_type_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="product_id">{{ trans('cruds.stockMovement.fields.product') }}</label>
                <select class="form-control select2 {{ $errors->has('product') ? 'is-invalid' : '' }}" name="product_id" id="product_id">
                    @foreach($products as $id => $entry)
                        <option value="{{ $id }}" {{ (old('product_id') ? old('product_id') : $stockMovement->product->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('product'))
                    <span class="text-danger">{{ $errors->first('product') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.stockMovement.fields.product_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="material_id">{{ trans('cruds.stockMovement.fields.material') }}</label>
                <select class="form-control select2 {{ $errors->has('material') ? 'is-invalid' : '' }}" name="material_id" id="material_id">
                    @foreach($materials as $id => $entry)
                        <option value="{{ $id }}" {{ (old('material_id') ? old('material_id') : $stockMovement->material->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('material'))
                    <span class="text-danger">{{ $errors->first('material') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.stockMovement.fields.material_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="quantity">{{ trans('cruds.stockMovement.fields.quantity') }}</label>
                <input class="form-control {{ $errors->has('quantity') ? 'is-invalid' : '' }}" type="number" name="quantity" id="quantity" value="{{ old('quantity', $stockMovement->quantity) }}" step="0.01" required>
                @if($errors->has('quantity'))
                    <span class="text-danger">{{ $errors->first('quantity') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.stockMovement.fields.quantity_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.stockMovement.fields.transaction_type') }}</label>
                <select class="form-control {{ $errors->has('transaction_type') ? 'is-invalid' : '' }}" name="transaction_type" id="transaction_type">
                    <option value disabled {{ old('transaction_type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\StockMovement::TRANSACTION_TYPE_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('transaction_type', $stockMovement->transaction_type) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('transaction_type'))
                    <span class="text-danger">{{ $errors->first('transaction_type') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.stockMovement.fields.transaction_type_helper') }}</span>
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