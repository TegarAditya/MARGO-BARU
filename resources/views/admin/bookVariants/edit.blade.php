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
            <div class="row">
                <div class="col-6">

                </div>
                <div class="col-6">

                </div>
                <div class="col-6">

                </div>
                <div class="col-6">

                </div>
                <div class="col-6">

                </div>
                <div class="col-6">

                </div>
            </div>
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
                <label for="isi_id">{{ trans('cruds.bookVariant.fields.isi') }}</label>
                <select class="form-control select2 {{ $errors->has('isi') ? 'is-invalid' : '' }}" name="isi_id" id="isi_id">
                    @foreach($isis as $id => $entry)
                        <option value="{{ $id }}" {{ (old('isi_id') ? old('isi_id') : $bookVariant->isi->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('isi'))
                    <span class="text-danger">{{ $errors->first('isi') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bookVariant.fields.isi_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="cover_id">{{ trans('cruds.bookVariant.fields.cover') }}</label>
                <select class="form-control select2 {{ $errors->has('cover') ? 'is-invalid' : '' }}" name="cover_id" id="cover_id">
                    @foreach($covers as $id => $entry)
                        <option value="{{ $id }}" {{ (old('cover_id') ? old('cover_id') : $bookVariant->cover->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('cover'))
                    <span class="text-danger">{{ $errors->first('cover') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bookVariant.fields.cover_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="mapel_id">{{ trans('cruds.bookVariant.fields.mapel') }}</label>
                <select class="form-control select2 {{ $errors->has('mapel') ? 'is-invalid' : '' }}" name="mapel_id" id="mapel_id">
                    @foreach($mapels as $id => $entry)
                        <option value="{{ $id }}" {{ (old('mapel_id') ? old('mapel_id') : $bookVariant->mapel->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('mapel'))
                    <span class="text-danger">{{ $errors->first('mapel') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bookVariant.fields.mapel_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="kelas_id">{{ trans('cruds.bookVariant.fields.kelas') }}</label>
                <select class="form-control select2 {{ $errors->has('kelas') ? 'is-invalid' : '' }}" name="kelas_id" id="kelas_id">
                    @foreach($kelas as $id => $entry)
                        <option value="{{ $id }}" {{ (old('kelas_id') ? old('kelas_id') : $bookVariant->kelas->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('kelas'))
                    <span class="text-danger">{{ $errors->first('kelas') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bookVariant.fields.kelas_helper') }}</span>
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
                <label for="components">Components</label>
                <div style="padding-bottom: 4px">
                    <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                    <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                </div>
                <select class="form-control select2 {{ $errors->has('components') ? 'is-invalid' : '' }}" name="components[]" id="components" multiple>
                    @foreach($components as $id => $component)
                        <option value="{{ $id }}" {{ (in_array($id, old('components', [])) || $bookVariant->components->contains($id)) ? 'selected' : '' }}>{{ $component }}</option>
                    @endforeach
                </select>
                @if($errors->has('components'))
                    <span class="text-danger">{{ $errors->first('components') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bookComponent.fields.components_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="photo">{{ trans('cruds.bookVariant.fields.photo') }}</label>
                <div class="needsclick dropzone {{ $errors->has('photo') ? 'is-invalid' : '' }}" id="photo-dropzone">
                </div>
                @if($errors->has('photo'))
                    <span class="text-danger">{{ $errors->first('photo') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bookVariant.fields.photo_helper') }}</span>
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

@section('scripts')
<script>
    var uploadedPhotoMap = {}
Dropzone.options.photoDropzone = {
    url: '{{ route('admin.book-variants.storeMedia') }}',
    maxFilesize: 2, // MB
    acceptedFiles: '.jpeg,.jpg,.png,.gif',
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 2,
      width: 4096,
      height: 4096
    },
    success: function (file, response) {
      $('form').append('<input type="hidden" name="photo[]" value="' + response.name + '">')
      uploadedPhotoMap[file.name] = response.name
    },
    removedfile: function (file) {
      console.log(file)
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedPhotoMap[file.name]
      }
      $('form').find('input[name="photo[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($bookVariant) && $bookVariant->photo)
      var files = {!! json_encode($bookVariant->photo) !!}
          for (var i in files) {
          var file = files[i]
          this.options.addedfile.call(this, file)
          this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
          file.previewElement.classList.add('dz-complete')
          $('form').append('<input type="hidden" name="photo[]" value="' + file.file_name + '">')
        }
@endif
    },
     error: function (file, response) {
         if ($.type(response) === 'string') {
             var message = response //dropzone sends it's own error messages in string
         } else {
             var message = response.errors.file
         }
         file.previewElement.classList.add('dz-error')
         _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
         _results = []
         for (_i = 0, _len = _ref.length; _i < _len; _i++) {
             node = _ref[_i]
             _results.push(node.textContent = message)
         }

         return _results
     }
}

</script>
@endsection
