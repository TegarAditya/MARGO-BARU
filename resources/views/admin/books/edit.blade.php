@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.book.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.books.update", [$book->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label for="name">{{ trans('cruds.book.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', $book->name) }}">
                @if($errors->has('name'))
                    <span class="text-danger">{{ $errors->first('name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.book.fields.name_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="description">{{ trans('cruds.book.fields.description') }}</label>
                <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description" id="description">{{ old('description', $book->description) }}</textarea>
                @if($errors->has('description'))
                    <span class="text-danger">{{ $errors->first('description') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.book.fields.description_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="jenjang_id">{{ trans('cruds.book.fields.jenjang') }}</label>
                <select class="form-control select2 {{ $errors->has('jenjang') ? 'is-invalid' : '' }}" name="jenjang_id" id="jenjang_id" required>
                    @foreach($jenjangs as $id => $entry)
                        <option value="{{ $id }}" {{ (old('jenjang_id') ? old('jenjang_id') : $book->jenjang->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('jenjang'))
                    <span class="text-danger">{{ $errors->first('jenjang') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.book.fields.jenjang_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="kurikulum_id">{{ trans('cruds.book.fields.kurikulum') }}</label>
                <select class="form-control select2 {{ $errors->has('kurikulum') ? 'is-invalid' : '' }}" name="kurikulum_id" id="kurikulum_id" required>
                    @foreach($kurikulums as $id => $entry)
                        <option value="{{ $id }}" {{ (old('kurikulum_id') ? old('kurikulum_id') : $book->kurikulum->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('kurikulum'))
                    <span class="text-danger">{{ $errors->first('kurikulum') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.book.fields.kurikulum_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="mapel_id">{{ trans('cruds.book.fields.mapel') }}</label>
                <select class="form-control select2 {{ $errors->has('mapel') ? 'is-invalid' : '' }}" name="mapel_id" id="mapel_id" required>
                    @foreach($mapels as $id => $entry)
                        <option value="{{ $id }}" {{ (old('mapel_id') ? old('mapel_id') : $book->mapel->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('mapel'))
                    <span class="text-danger">{{ $errors->first('mapel') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.book.fields.mapel_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="kelas_id">{{ trans('cruds.book.fields.kelas') }}</label>
                <select class="form-control select2 {{ $errors->has('kelas') ? 'is-invalid' : '' }}" name="kelas_id" id="kelas_id" required>
                    @foreach($kelas as $id => $entry)
                        <option value="{{ $id }}" {{ (old('kelas_id') ? old('kelas_id') : $book->kelas->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('kelas'))
                    <span class="text-danger">{{ $errors->first('kelas') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.book.fields.kelas_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="cover_id">{{ trans('cruds.book.fields.cover') }}</label>
                <select class="form-control select2 {{ $errors->has('cover') ? 'is-invalid' : '' }}" name="cover_id" id="cover_id" required>
                    @foreach($covers as $id => $entry)
                        <option value="{{ $id }}" {{ (old('cover_id') ? old('cover_id') : $book->cover->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('cover'))
                    <span class="text-danger">{{ $errors->first('cover') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.book.fields.cover_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="semester_id">{{ trans('cruds.book.fields.semester') }}</label>
                <select class="form-control select2 {{ $errors->has('semester') ? 'is-invalid' : '' }}" name="semester_id" id="semester_id" required>
                    @foreach($semesters as $id => $entry)
                        <option value="{{ $id }}" {{ (old('semester_id') ? old('semester_id') : $book->semester->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('semester'))
                    <span class="text-danger">{{ $errors->first('semester') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.book.fields.semester_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="photo">{{ trans('cruds.book.fields.photo') }}</label>
                <div class="needsclick dropzone {{ $errors->has('photo') ? 'is-invalid' : '' }}" id="photo-dropzone">
                </div>
                @if($errors->has('photo'))
                    <span class="text-danger">{{ $errors->first('photo') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.book.fields.photo_helper') }}</span>
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
    url: '{{ route('admin.books.storeMedia') }}',
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
@if(isset($book) && $book->photo)
      var files = {!! json_encode($book->photo) !!}
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