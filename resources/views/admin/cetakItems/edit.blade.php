@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.cetakItem.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.cetak-items.update", [$cetakItem->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="semester_id">{{ trans('cruds.cetakItem.fields.semester') }}</label>
                <select class="form-control select2 {{ $errors->has('semester') ? 'is-invalid' : '' }}" name="semester_id" id="semester_id" required>
                    @foreach($semesters as $id => $entry)
                        <option value="{{ $id }}" {{ (old('semester_id') ? old('semester_id') : $cetakItem->semester->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('semester'))
                    <span class="text-danger">{{ $errors->first('semester') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.cetakItem.fields.semester_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="product_id">{{ trans('cruds.cetakItem.fields.product') }}</label>
                <select class="form-control select2 {{ $errors->has('product') ? 'is-invalid' : '' }}" name="product_id" id="product_id" required>
                    @foreach($products as $id => $entry)
                        <option value="{{ $id }}" {{ (old('product_id') ? old('product_id') : $cetakItem->product->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('product'))
                    <span class="text-danger">{{ $errors->first('product') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.cetakItem.fields.product_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="halaman_id">{{ trans('cruds.cetakItem.fields.halaman') }}</label>
                <select class="form-control select2 {{ $errors->has('halaman') ? 'is-invalid' : '' }}" name="halaman_id" id="halaman_id" required>
                    @foreach($halamen as $id => $entry)
                        <option value="{{ $id }}" {{ (old('halaman_id') ? old('halaman_id') : $cetakItem->halaman->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('halaman'))
                    <span class="text-danger">{{ $errors->first('halaman') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.cetakItem.fields.halaman_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="quantity">{{ trans('cruds.cetakItem.fields.quantity') }}</label>
                <input class="form-control {{ $errors->has('quantity') ? 'is-invalid' : '' }}" type="number" name="quantity" id="quantity" value="{{ old('quantity', $cetakItem->quantity) }}" step="1" required>
                @if($errors->has('quantity'))
                    <span class="text-danger">{{ $errors->first('quantity') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.cetakItem.fields.quantity_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="cost">{{ trans('cruds.cetakItem.fields.cost') }}</label>
                <input class="form-control {{ $errors->has('cost') ? 'is-invalid' : '' }}" type="number" name="cost" id="cost" value="{{ old('cost', $cetakItem->cost) }}" step="0.01" required>
                @if($errors->has('cost'))
                    <span class="text-danger">{{ $errors->first('cost') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.cetakItem.fields.cost_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="plate_id">{{ trans('cruds.cetakItem.fields.plate') }}</label>
                <select class="form-control select2 {{ $errors->has('plate') ? 'is-invalid' : '' }}" name="plate_id" id="plate_id">
                    @foreach($plates as $id => $entry)
                        <option value="{{ $id }}" {{ (old('plate_id') ? old('plate_id') : $cetakItem->plate->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('plate'))
                    <span class="text-danger">{{ $errors->first('plate') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.cetakItem.fields.plate_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="plate_cost">{{ trans('cruds.cetakItem.fields.plate_cost') }}</label>
                <input class="form-control {{ $errors->has('plate_cost') ? 'is-invalid' : '' }}" type="number" name="plate_cost" id="plate_cost" value="{{ old('plate_cost', $cetakItem->plate_cost) }}" step="1">
                @if($errors->has('plate_cost'))
                    <span class="text-danger">{{ $errors->first('plate_cost') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.cetakItem.fields.plate_cost_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="paper_id">{{ trans('cruds.cetakItem.fields.paper') }}</label>
                <select class="form-control select2 {{ $errors->has('paper') ? 'is-invalid' : '' }}" name="paper_id" id="paper_id">
                    @foreach($papers as $id => $entry)
                        <option value="{{ $id }}" {{ (old('paper_id') ? old('paper_id') : $cetakItem->paper->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('paper'))
                    <span class="text-danger">{{ $errors->first('paper') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.cetakItem.fields.paper_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="paper_cost">{{ trans('cruds.cetakItem.fields.paper_cost') }}</label>
                <input class="form-control {{ $errors->has('paper_cost') ? 'is-invalid' : '' }}" type="number" name="paper_cost" id="paper_cost" value="{{ old('paper_cost', $cetakItem->paper_cost) }}" step="0.01">
                @if($errors->has('paper_cost'))
                    <span class="text-danger">{{ $errors->first('paper_cost') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.cetakItem.fields.paper_cost_helper') }}</span>
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