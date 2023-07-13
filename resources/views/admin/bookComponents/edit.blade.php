@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.bookComponent.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.book-components.update", [$bookComponent->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label for="name">{{ trans('cruds.bookComponent.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', $bookComponent->name) }}">
                @if($errors->has('name'))
                    <span class="text-danger">{{ $errors->first('name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bookComponent.fields.name_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="description">{{ trans('cruds.bookComponent.fields.description') }}</label>
                <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description" id="description">{{ old('description', $bookComponent->description) }}</textarea>
                @if($errors->has('description'))
                    <span class="text-danger">{{ $errors->first('description') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bookComponent.fields.description_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.bookComponent.fields.type') }}</label>
                <select class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type" id="type" required>
                    <option value disabled {{ old('type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\BookComponent::TYPE_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('type', $bookComponent->type) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('type'))
                    <span class="text-danger">{{ $errors->first('type') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bookComponent.fields.type_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="jenjang_id">{{ trans('cruds.bookComponent.fields.jenjang') }}</label>
                <select class="form-control select2 {{ $errors->has('jenjang') ? 'is-invalid' : '' }}" name="jenjang_id" id="jenjang_id" required>
                    @foreach($jenjangs as $id => $entry)
                        <option value="{{ $id }}" {{ (old('jenjang_id') ? old('jenjang_id') : $bookComponent->jenjang->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('jenjang'))
                    <span class="text-danger">{{ $errors->first('jenjang') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bookComponent.fields.jenjang_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="kurikulum_id">{{ trans('cruds.bookComponent.fields.kurikulum') }}</label>
                <select class="form-control select2 {{ $errors->has('kurikulum') ? 'is-invalid' : '' }}" name="kurikulum_id" id="kurikulum_id" required>
                    @foreach($kurikulums as $id => $entry)
                        <option value="{{ $id }}" {{ (old('kurikulum_id') ? old('kurikulum_id') : $bookComponent->kurikulum->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('kurikulum'))
                    <span class="text-danger">{{ $errors->first('kurikulum') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bookComponent.fields.kurikulum_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="isi_id">{{ trans('cruds.bookComponent.fields.isi') }}</label>
                <select class="form-control select2 {{ $errors->has('isi') ? 'is-invalid' : '' }}" name="isi_id" id="isi_id">
                    @foreach($isis as $id => $entry)
                        <option value="{{ $id }}" {{ (old('isi_id') ? old('isi_id') : $bookComponent->isi->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('isi'))
                    <span class="text-danger">{{ $errors->first('isi') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bookComponent.fields.isi_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="cover_id">{{ trans('cruds.bookComponent.fields.cover') }}</label>
                <select class="form-control select2 {{ $errors->has('cover') ? 'is-invalid' : '' }}" name="cover_id" id="cover_id">
                    @foreach($covers as $id => $entry)
                        <option value="{{ $id }}" {{ (old('cover_id') ? old('cover_id') : $bookComponent->cover->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('cover'))
                    <span class="text-danger">{{ $errors->first('cover') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bookComponent.fields.cover_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="mapel_id">{{ trans('cruds.bookComponent.fields.mapel') }}</label>
                <select class="form-control select2 {{ $errors->has('mapel') ? 'is-invalid' : '' }}" name="mapel_id" id="mapel_id" required>
                    @foreach($mapels as $id => $entry)
                        <option value="{{ $id }}" {{ (old('mapel_id') ? old('mapel_id') : $bookComponent->mapel->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('mapel'))
                    <span class="text-danger">{{ $errors->first('mapel') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bookComponent.fields.mapel_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="kelas_id">{{ trans('cruds.bookComponent.fields.kelas') }}</label>
                <select class="form-control select2 {{ $errors->has('kelas') ? 'is-invalid' : '' }}" name="kelas_id" id="kelas_id" required>
                    @foreach($kelas as $id => $entry)
                        <option value="{{ $id }}" {{ (old('kelas_id') ? old('kelas_id') : $bookComponent->kelas->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('kelas'))
                    <span class="text-danger">{{ $errors->first('kelas') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bookComponent.fields.kelas_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="halaman_id">{{ trans('cruds.bookComponent.fields.halaman') }}</label>
                <select class="form-control select2 {{ $errors->has('halaman') ? 'is-invalid' : '' }}" name="halaman_id" id="halaman_id">
                    @foreach($halamen as $id => $entry)
                        <option value="{{ $id }}" {{ (old('halaman_id') ? old('halaman_id') : $bookComponent->halaman->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('halaman'))
                    <span class="text-danger">{{ $errors->first('halaman') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bookComponent.fields.halaman_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="semester_id">{{ trans('cruds.bookComponent.fields.semester') }}</label>
                <select class="form-control select2 {{ $errors->has('semester') ? 'is-invalid' : '' }}" name="semester_id" id="semester_id" required>
                    @foreach($semesters as $id => $entry)
                        <option value="{{ $id }}" {{ (old('semester_id') ? old('semester_id') : $bookComponent->semester->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('semester'))
                    <span class="text-danger">{{ $errors->first('semester') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bookComponent.fields.semester_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="warehouse_id">{{ trans('cruds.bookComponent.fields.warehouse') }}</label>
                <select class="form-control select2 {{ $errors->has('warehouse') ? 'is-invalid' : '' }}" name="warehouse_id" id="warehouse_id">
                    @foreach($warehouses as $id => $entry)
                        <option value="{{ $id }}" {{ (old('warehouse_id') ? old('warehouse_id') : $bookComponent->warehouse->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('warehouse'))
                    <span class="text-danger">{{ $errors->first('warehouse') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bookComponent.fields.warehouse_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="stock">{{ trans('cruds.bookComponent.fields.stock') }}</label>
                <input class="form-control {{ $errors->has('stock') ? 'is-invalid' : '' }}" type="number" name="stock" id="stock" value="{{ old('stock', $bookComponent->stock) }}" step="1" required>
                @if($errors->has('stock'))
                    <span class="text-danger">{{ $errors->first('stock') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bookComponent.fields.stock_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="unit_id">{{ trans('cruds.bookComponent.fields.unit') }}</label>
                <select class="form-control select2 {{ $errors->has('unit') ? 'is-invalid' : '' }}" name="unit_id" id="unit_id">
                    @foreach($units as $id => $entry)
                        <option value="{{ $id }}" {{ (old('unit_id') ? old('unit_id') : $bookComponent->unit->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('unit'))
                    <span class="text-danger">{{ $errors->first('unit') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bookComponent.fields.unit_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="price">{{ trans('cruds.bookComponent.fields.price') }}</label>
                <input class="form-control {{ $errors->has('price') ? 'is-invalid' : '' }}" type="number" name="price" id="price" value="{{ old('price', $bookComponent->price) }}" step="0.01">
                @if($errors->has('price'))
                    <span class="text-danger">{{ $errors->first('price') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bookComponent.fields.price_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="cost">{{ trans('cruds.bookComponent.fields.cost') }}</label>
                <input class="form-control {{ $errors->has('cost') ? 'is-invalid' : '' }}" type="number" name="cost" id="cost" value="{{ old('cost', $bookComponent->cost) }}" step="0.01">
                @if($errors->has('cost'))
                    <span class="text-danger">{{ $errors->first('cost') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bookComponent.fields.cost_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="components">{{ trans('cruds.bookComponent.fields.components') }}</label>
                <div style="padding-bottom: 4px">
                    <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                    <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                </div>
                <select class="form-control select2 {{ $errors->has('components') ? 'is-invalid' : '' }}" name="components[]" id="components" multiple>
                    @foreach($components as $id => $component)
                        <option value="{{ $id }}" {{ (in_array($id, old('components', [])) || $bookComponent->material_of->contains($id)) ? 'selected' : '' }}>{{ $component }}</option>
                    @endforeach
                </select>
                @if($errors->has('components'))
                    <span class="text-danger">{{ $errors->first('components') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bookComponent.fields.components_helper') }}</span>
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
