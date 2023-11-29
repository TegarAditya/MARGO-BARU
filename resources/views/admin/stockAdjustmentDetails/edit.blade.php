@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.stockAdjustmentDetail.title_singular') }}
    </div>

    <div class="card-body">
        <form class="form-prevent-multiple-submits" method="POST" action="{{ route("admin.stock-adjustment-details.update", [$stockAdjustmentDetail->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label for="product_id">{{ trans('cruds.stockAdjustmentDetail.fields.product') }}</label>
                <select class="form-control select2 {{ $errors->has('product') ? 'is-invalid' : '' }}" name="product_id" id="product_id">
                    @foreach($products as $id => $entry)
                        <option value="{{ $id }}" {{ (old('product_id') ? old('product_id') : $stockAdjustmentDetail->product->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('product'))
                    <span class="text-danger">{{ $errors->first('product') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.stockAdjustmentDetail.fields.product_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="material_id">{{ trans('cruds.stockAdjustmentDetail.fields.material') }}</label>
                <select class="form-control select2 {{ $errors->has('material') ? 'is-invalid' : '' }}" name="material_id" id="material_id">
                    @foreach($materials as $id => $entry)
                        <option value="{{ $id }}" {{ (old('material_id') ? old('material_id') : $stockAdjustmentDetail->material->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('material'))
                    <span class="text-danger">{{ $errors->first('material') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.stockAdjustmentDetail.fields.material_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="stock_adjustment_id">{{ trans('cruds.stockAdjustmentDetail.fields.stock_adjustment') }}</label>
                <select class="form-control select2 {{ $errors->has('stock_adjustment') ? 'is-invalid' : '' }}" name="stock_adjustment_id" id="stock_adjustment_id" required>
                    @foreach($stock_adjustments as $id => $entry)
                        <option value="{{ $id }}" {{ (old('stock_adjustment_id') ? old('stock_adjustment_id') : $stockAdjustmentDetail->stock_adjustment->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('stock_adjustment'))
                    <span class="text-danger">{{ $errors->first('stock_adjustment') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.stockAdjustmentDetail.fields.stock_adjustment_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="quantity">{{ trans('cruds.stockAdjustmentDetail.fields.quantity') }}</label>
                <input class="form-control {{ $errors->has('quantity') ? 'is-invalid' : '' }}" type="number" name="quantity" id="quantity" value="{{ old('quantity', $stockAdjustmentDetail->quantity) }}" step="0.01" required>
                @if($errors->has('quantity'))
                    <span class="text-danger">{{ $errors->first('quantity') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.stockAdjustmentDetail.fields.quantity_helper') }}</span>
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