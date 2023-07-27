@extends('layouts.admin')
@section('content')
@can('material_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.materials.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.material.title_singular') }}
            </a>
            <a class="btn btn-danger" href="{{ route('admin.materials.templateImport') }}">
                Template Import
            </a>
            <button class="btn btn-primary" data-toggle="modal" data-target="#importModal">
                Import
            </button>
            <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                {{ trans('global.app_csvImport') }}
            </button>
            @include('csvImport.modal', ['model' => 'Material', 'route' => 'admin.materials.parseCsvImport'])
            @include('csvImport.import_modal', ['model' => 'Material', 'route' => 'admin.materials.import'])
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.material.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <form id="filterform">
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label>{{ trans('cruds.material.fields.category') }}</label>
                        <select class="form-control {{ $errors->has('category') ? 'is-invalid' : '' }}" name="category" id="category">
                            <option value {{ old('category', null) === null ? 'selected' : '' }}>All</option>
                            @foreach(App\Models\Material::CATEGORY_SELECT as $key => $label)
                                <option value="{{ $key }}" {{ old('category', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('category'))
                            <span class="text-danger">{{ $errors->first('category') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.material.fields.category_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="vendor_id">{{ trans('cruds.platePrint.fields.vendor') }}</label>
                        <select class="form-control select2 {{ $errors->has('vendor') ? 'is-invalid' : '' }}" name="vendor_id" id="vendor_id">
                            @foreach($vendors as $id => $entry)
                                <option value="{{ $id }}" {{ old('vendor_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('vendor'))
                            <span class="text-danger">{{ $errors->first('vendor') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.platePrint.fields.vendor_helper') }}</span>
                    </div>
                </div>
            </div>

            <div class="form-group mt-3">
                <button class="btn btn-primary" type="submit">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Material">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.material.fields.code') }}
                    </th>
                    <th>
                        {{ trans('cruds.material.fields.name') }}
                    </th>
                    <th>
                        {{ trans('cruds.material.fields.unit') }}
                    </th>
                    <th>
                        {{ trans('cruds.material.fields.stock') }}
                    </th>
                    <th>
                        {{ trans('cruds.material.fields.vendor') }}
                    </th>
                    <th>
                        &nbsp;
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
            url: "{{ route('admin.materials.index') }}",
            data: function(data) {
                data.category = $('#category').val(),
                data.vendor = $('#vendor_id').val()
            }
        },
    columns: [
        { data: 'placeholder', name: 'placeholder' },
        { data: 'code', name: 'code', class: 'text-center' },
        { data: 'name', name: 'name' },
        { data: 'unit_name', name: 'unit.name', class: 'text-center' },
        { data: 'stock', name: 'stock', class: 'text-center' },
        { data: 'vendor', name: 'vendor', class: 'text-center' },
        { data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    pageLength: 25,
  };
  let table = $('.datatable-Material').DataTable(dtOverrideGlobals);
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
