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

            <div class="form-group">
                <button class="btn btn-primary" type="submit">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <div class="card-body">
        <form action="{{ route("admin.materials.jangka") }}" enctype="multipart/form-data" method="POST">
            @csrf
            <div class="row">
                <div class="col-6">
                    <x-admin.form-group
                        type="text"
                        id="date"
                        name="date"
                        containerClass=" m-0"
                        boxClass=" px-2 py-1"
                        class="form-control-sm"
                        value="{{ request('date', old('date'))}}"
                        placeholder="Pilih Tanggal"
                    >
                        <x-slot name="label">
                            <label class="small mb-0" for="date">Tanggal</label>
                        </x-slot>

                        <x-slot name="right">
                            <button type="button" class="btn btn-sm border-0 btn-default px-2 date-clear" data-action="+" style="display:{{ !request('date', old('date')) ? 'none' : 'block' }}">
                                <i class="fa fa-times"></i>
                            </button>
                        </x-slot>
                    </x-admin.form-group>
                </div>
            </div>
            <div class="form-group mt-3 mb-5">
                <button class="btn btn-danger" type="submit">
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
<script src="https://cdn.jsdelivr.net/npm/@easepick/bundle@1.2.0/dist/index.umd.min.js"></script>
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
        { data: 'actions', name: '{{ trans('global.actions') }}', class: 'text-center' }
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

    var picker = new easepick.create({
        element: $('#date').get(0),
        css: [
            'https://cdn.jsdelivr.net/npm/@easepick/bundle@1.2.0/dist/index.css',
        ],
        plugins: ['RangePlugin', 'LockPlugin'],
        RangePlugin: {
            tooltip: true,
        },
        LockPlugin: {
            maxDate: new Date(),
        },
    });

    picker.on('select', function(e) {
        $('#date').trigger('change');
        $('.date-clear').show();
    });

    $('.date-clear').on('click', function(e) {
        e.preventDefault();

        picker.clear();
        $(e.currentTarget).hide();
    });

});

</script>
@endsection
