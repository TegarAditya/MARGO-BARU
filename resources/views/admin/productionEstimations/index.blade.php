@extends('layouts.admin')
@section('content')
{{-- @can('production_estimation_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.production-estimations.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.productionEstimation.title_singular') }}
            </a>
        </div>
    </div>
@endcan --}}
<div class="card">
    <div class="card-header">
        {{ trans('cruds.productionEstimation.title_singular') }} {{ trans('global.list') }}
    </div>
    <div class="card-body">
        <form id="filterform">
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label>{{ trans('cruds.bookVariant.fields.type') }}</label>
                        <select class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type" id="type">
                            <option value disabled {{ old('type', null) === null ? 'selected' : '' }}>All</option>
                            @foreach(App\Models\BookVariant::TYPE_SELECT as $key => $label)
                                <option value="{{ $key }}" {{ old('type', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('type'))
                            <span class="text-danger">{{ $errors->first('type') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.bookVariant.fields.type_helper') }}</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label for="jenjang_id">{{ trans('cruds.book.fields.jenjang') }}</label>
                        <select class="form-control select2 {{ $errors->has('jenjang') ? 'is-invalid' : '' }}" name="jenjang_id" id="jenjang_id">
                            @foreach($jenjangs as $id => $entry)
                                <option value="{{ $id }}" {{ old('jenjang_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('jenjang'))
                            <span class="text-danger">{{ $errors->first('jenjang') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.book.fields.jenjang_helper') }}</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label for="kurikulum_id">{{ trans('cruds.book.fields.kurikulum') }}</label>
                        <select class="form-control select2 {{ $errors->has('kurikulum') ? 'is-invalid' : '' }}" name="kurikulum_id" id="kurikulum_id">
                            @foreach($kurikulums as $id => $entry)
                                <option value="{{ $id }}" {{ old('kurikulum_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('kurikulum'))
                            <span class="text-danger">{{ $errors->first('kurikulum') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.book.fields.kurikulum_helper') }}</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label for="mapel_id">{{ trans('cruds.book.fields.mapel') }}</label>
                        <select class="form-control select2 {{ $errors->has('mapel') ? 'is-invalid' : '' }}" name="mapel_id" id="mapel_id">
                            @foreach($mapels as $id => $entry)
                                <option value="{{ $id }}" {{ old('mapel_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('mapel'))
                            <span class="text-danger">{{ $errors->first('mapel') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.book.fields.mapel_helper') }}</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label for="kelas_id">{{ trans('cruds.bookVariant.fields.kelas') }}</label>
                        <select class="form-control select2 {{ $errors->has('kelas') ? 'is-invalid' : '' }}" name="kelas_id" id="kelas_id">
                            @foreach($kelas as $id => $entry)
                                <option value="{{ $id }}" {{ old('kelas_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('kelas'))
                            <span class="text-danger">{{ $errors->first('kelas') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.bookVariant.fields.kelas_helper') }}</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label for="semester_id">{{ trans('cruds.book.fields.semester') }}</label>
                        <select class="form-control select2 {{ $errors->has('semester') ? 'is-invalid' : '' }}" name="semester_id" id="semester_id">
                            @foreach($semesters as $id => $entry)
                                <option value="{{ $id }}" {{ old('semester_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('semester'))
                            <span class="text-danger">{{ $errors->first('semester') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.book.fields.semester_helper') }}</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label for="cover_id">{{ trans('cruds.book.fields.isi') }}</label>
                        <select class="form-control select2 {{ $errors->has('isi') ? 'is-invalid' : '' }}" name="isi_id" id="isi_id">
                            @foreach($isis as $id => $entry)
                                <option value="{{ $id }}" {{ old('isi_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('isi'))
                            <span class="text-danger">{{ $errors->first('cover') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.book.fields.cover_helper') }}</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label for="cover_id">{{ trans('cruds.book.fields.cover') }}</label>
                        <select class="form-control select2 {{ $errors->has('cover') ? 'is-invalid' : '' }}" name="cover_id" id="cover_id">
                            @foreach($covers as $id => $entry)
                                <option value="{{ $id }}" {{ old('cover_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('cover'))
                            <span class="text-danger">{{ $errors->first('cover') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.book.fields.cover_helper') }}</span>
                    </div>
                </div>
            </div>

            <div class="form-group mt-3">
                <button class="btn btn-success" type="submit">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-ProductionEstimation">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th class="text-center" width="200">
                        {{ trans('cruds.productionEstimation.fields.product') }} Code
                    </th>
                    <th class="text-center">
                        {{ trans('cruds.productionEstimation.fields.product') }} Name
                    </th>
                    <th class="text-center">
                        {{ trans('cruds.productionEstimation.fields.estimasi') }}
                    </th>
                </tr>
            </thead>
        </table>
    </div>
</div>



@endsection
@section('scripts')
@parent
<script>
$(function () {

  let dtOverrideGlobals = {
    processing: true,
    serverSide: true,
    retrieve: true,
    aaSorting: [],
    ajax: {
        url: "{{ route('admin.production-estimations.index') }}",
        data: function(data) {
            data.type = $('#type').val(),
            data.semester = $('#semester_id').val(),
            data.isi = $('#isi_id').val(),
            data.cover = $('#cover_id').val(),
            data.kurikulum = $('#kurikulum_id').val(),
            data.jenjang = $('#jenjang_id').val(),
            data.kelas = $('#kelas_id').val(),
            data.mapel = $('#mapel_id').val()
        }
    },
    columns: [
        { data: 'placeholder', name: 'placeholder' },
        { data: 'product_code', name: 'product.code', class: 'text-center' },
        { data: 'product_name', name: 'product.name' },
        { data: 'estimasi', name: 'estimasi', class: 'text-center' },
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 50,
  };
  let table = $('.datatable-ProductionEstimation').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
    $("#filterform").submit(function(event) {
        event.preventDefault();
        table.ajax.reload();
    });

});

</script>
@endsection
