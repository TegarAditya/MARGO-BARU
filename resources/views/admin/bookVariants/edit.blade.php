@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.bookVariant.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.book-variants.update", [$bookVariant->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="book_id">{{ trans('cruds.bookVariant.fields.book') }}</label>
                <select class="form-control select2 {{ $errors->has('book') ? 'is-invalid' : '' }}" name="book_id" id="book_id" required>
                    @foreach($books as $id => $entry)
                        <option value="{{ $id }}" {{ (old('book_id') ? old('book_id') : $bookVariant->book->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('book'))
                    <span class="text-danger">{{ $errors->first('book') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bookVariant.fields.book_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="parent_id">{{ trans('cruds.bookVariant.fields.parent') }}</label>
                <select class="form-control select2 {{ $errors->has('parent') ? 'is-invalid' : '' }}" name="parent_id" id="parent_id">
                    @foreach($parents as $id => $entry)
                        <option value="{{ $id }}" {{ (old('parent_id') ? old('parent_id') : $bookVariant->parent->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('parent'))
                    <span class="text-danger">{{ $errors->first('parent') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bookVariant.fields.parent_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.bookVariant.fields.type') }}</label>
                <select class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type" id="type" required>
                    <option value disabled {{ old('type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\BookVariant::TYPE_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('type', $bookVariant->type) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('type'))
                    <span class="text-danger">{{ $errors->first('type') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bookVariant.fields.type_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="jenjang_id">{{ trans('cruds.bookVariant.fields.jenjang') }}</label>
                <select class="form-control select2 {{ $errors->has('jenjang') ? 'is-invalid' : '' }}" name="jenjang_id" id="jenjang_id" required>
                    @foreach($jenjangs as $id => $entry)
                        <option value="{{ $id }}" {{ (old('jenjang_id') ? old('jenjang_id') : $bookVariant->jenjang->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('jenjang'))
                    <span class="text-danger">{{ $errors->first('jenjang') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bookVariant.fields.jenjang_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="semester_id">{{ trans('cruds.bookVariant.fields.semester') }}</label>
                <select class="form-control select2 {{ $errors->has('semester') ? 'is-invalid' : '' }}" name="semester_id" id="semester_id" required>
                    @foreach($semesters as $id => $entry)
                        <option value="{{ $id }}" {{ (old('semester_id') ? old('semester_id') : $bookVariant->semester->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('semester'))
                    <span class="text-danger">{{ $errors->first('semester') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bookVariant.fields.semester_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="kurikulum_id">{{ trans('cruds.bookVariant.fields.kurikulum') }}</label>
                <select class="form-control select2 {{ $errors->has('kurikulum') ? 'is-invalid' : '' }}" name="kurikulum_id" id="kurikulum_id" required>
                    @foreach($kurikulums as $id => $entry)
                        <option value="{{ $id }}" {{ (old('kurikulum_id') ? old('kurikulum_id') : $bookVariant->kurikulum->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('kurikulum'))
                    <span class="text-danger">{{ $errors->first('kurikulum') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bookVariant.fields.kurikulum_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="halaman_id">{{ trans('cruds.bookVariant.fields.halaman') }}</label>
                <select class="form-control select2 {{ $errors->has('halaman') ? 'is-invalid' : '' }}" name="halaman_id" id="halaman_id" required>
                    @foreach($halamen as $id => $entry)
                        <option value="{{ $id }}" {{ (old('halaman_id') ? old('halaman_id') : $bookVariant->halaman->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('halaman'))
                    <span class="text-danger">{{ $errors->first('halaman') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bookVariant.fields.halaman_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="stock">{{ trans('cruds.bookVariant.fields.stock') }}</label>
                <input class="form-control {{ $errors->has('stock') ? 'is-invalid' : '' }}" type="number" name="stock" id="stock" value="{{ old('stock', $bookVariant->stock) }}" step="1" required>
                @if($errors->has('stock'))
                    <span class="text-danger">{{ $errors->first('stock') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bookVariant.fields.stock_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="unit_id">{{ trans('cruds.bookVariant.fields.unit') }}</label>
                <select class="form-control select2 {{ $errors->has('unit') ? 'is-invalid' : '' }}" name="unit_id" id="unit_id">
                    @foreach($units as $id => $entry)
                        <option value="{{ $id }}" {{ (old('unit_id') ? old('unit_id') : $bookVariant->unit->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('unit'))
                    <span class="text-danger">{{ $errors->first('unit') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bookVariant.fields.unit_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="price">{{ trans('cruds.bookVariant.fields.price') }}</label>
                <input class="form-control {{ $errors->has('price') ? 'is-invalid' : '' }}" type="number" name="price" id="price" value="{{ old('price', $bookVariant->price) }}" step="0.01">
                @if($errors->has('price'))
                    <span class="text-danger">{{ $errors->first('price') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bookVariant.fields.price_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="cost">{{ trans('cruds.bookVariant.fields.cost') }}</label>
                <input class="form-control {{ $errors->has('cost') ? 'is-invalid' : '' }}" type="number" name="cost" id="cost" value="{{ old('cost', $bookVariant->cost) }}" step="0.01">
                @if($errors->has('cost'))
                    <span class="text-danger">{{ $errors->first('cost') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bookVariant.fields.cost_helper') }}</span>
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