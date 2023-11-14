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
                <label for="price">{{ trans('cruds.bookVariant.fields.price') }}</label>
                <input class="form-control {{ $errors->has('price') ? 'is-invalid' : '' }}" type="number" name="price" id="price" value="{{ old('price', $bookVariant->price) }}" step="1">
                @if($errors->has('price'))
                    <span class="text-danger">{{ $errors->first('price') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.bookVariant.fields.price_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="cost">{{ trans('cruds.bookVariant.fields.cost') }}</label>
                <input class="form-control {{ $errors->has('cost') ? 'is-invalid' : '' }}" type="number" name="cost" id="cost" value="{{ old('cost', $bookVariant->cost) }}" step="1">
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
