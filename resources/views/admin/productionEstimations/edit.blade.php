@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.productionEstimation.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.production-estimations.update", [$productionEstimation->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="product_id">{{ trans('cruds.productionEstimation.fields.product') }}</label>
                <select class="form-control select2 {{ $errors->has('product') ? 'is-invalid' : '' }}" name="product_id" id="product_id" required>
                    @foreach($products as $id => $entry)
                        <option value="{{ $id }}" {{ (old('product_id') ? old('product_id') : $productionEstimation->product->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('product'))
                    <span class="text-danger">{{ $errors->first('product') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.productionEstimation.fields.product_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="quantity">{{ trans('cruds.productionEstimation.fields.quantity') }}</label>
                <input class="form-control {{ $errors->has('quantity') ? 'is-invalid' : '' }}" type="number" name="quantity" id="quantity" value="{{ old('quantity', $productionEstimation->quantity) }}" step="1" required>
                @if($errors->has('quantity'))
                    <span class="text-danger">{{ $errors->first('quantity') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.productionEstimation.fields.quantity_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="estimasi">{{ trans('cruds.productionEstimation.fields.estimasi') }}</label>
                <input class="form-control {{ $errors->has('estimasi') ? 'is-invalid' : '' }}" type="number" name="estimasi" id="estimasi" value="{{ old('estimasi', $productionEstimation->estimasi) }}" step="1">
                @if($errors->has('estimasi'))
                    <span class="text-danger">{{ $errors->first('estimasi') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.productionEstimation.fields.estimasi_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="isi">{{ trans('cruds.productionEstimation.fields.isi') }}</label>
                <input class="form-control {{ $errors->has('isi') ? 'is-invalid' : '' }}" type="number" name="isi" id="isi" value="{{ old('isi', $productionEstimation->isi) }}" step="1">
                @if($errors->has('isi'))
                    <span class="text-danger">{{ $errors->first('isi') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.productionEstimation.fields.isi_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="cover">{{ trans('cruds.productionEstimation.fields.cover') }}</label>
                <input class="form-control {{ $errors->has('cover') ? 'is-invalid' : '' }}" type="number" name="cover" id="cover" value="{{ old('cover', $productionEstimation->cover) }}" step="1">
                @if($errors->has('cover'))
                    <span class="text-danger">{{ $errors->first('cover') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.productionEstimation.fields.cover_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="finishing">{{ trans('cruds.productionEstimation.fields.finishing') }}</label>
                <input class="form-control {{ $errors->has('finishing') ? 'is-invalid' : '' }}" type="number" name="finishing" id="finishing" value="{{ old('finishing', $productionEstimation->finishing) }}" step="1">
                @if($errors->has('finishing'))
                    <span class="text-danger">{{ $errors->first('finishing') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.productionEstimation.fields.finishing_helper') }}</span>
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