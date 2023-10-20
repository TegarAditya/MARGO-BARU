@extends('layouts.admin')
@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1 class="m-0">Estimasi Sales</h1>
    </div>
</div>
@can('estimation_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.estimations.create') }}">
                {{ trans('global.add') }} Order Sales
            </a>
            <a class="btn btn-warning" href="{{ route('admin.estimations.create', ['salesperson' => 'internal']) }}">
                {{ trans('global.add') }} {{ trans('cruds.estimation.title_singular') }} Internal
            </a>
            <button class="btn btn-primary" data-toggle="modal" data-target="#importModal">
                Import
            </button>
        </div>
    </div>
@endcan

<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Import Excel or CSV File</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class='col-md-12'>

                        <form class="form-horizontal" method="POST" action="{{ route('admin.estimations.import') }}" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            <div class="form-group">
                                <label class="required col-md-4" for="date">{{ trans('cruds.estimation.fields.date') }}</label>
                                <div class="col-md-8">
                                    <input class="form-control date {{ $errors->has('date') ? 'is-invalid' : '' }}" type="text" name="date" id="date" value="{{ old('date', $today) }}" required>
                                    @if($errors->has('date'))
                                        <span class="text-danger">{{ $errors->first('date') }}</span>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.estimation.fields.date_helper') }}</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="required col-md-4" for="salesperson_id">{{ trans('cruds.estimation.fields.salesperson') }}</label>
                                <div class="col-md-8">
                                    <select style="width: 100%;" class="form-control select2 {{ $errors->has('salesperson') ? 'is-invalid' : '' }}" name="salesperson_id" required>
                                        @foreach($salespeople as $id => $entry)
                                            <option value="{{ $id }}" {{ old('salesperson_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('salesperson'))
                                        <span class="text-danger">{{ $errors->first('salesperson') }}</span>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.estimation.fields.salesperson_helper') }}</span>
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('import_file') ? ' has-error' : '' }}">
                                <label for="import_file" class="col-md-4 control-label">Excel or CSV File to Import</label>

                                <div class="col-md-6">
                                    <input id="import_file" type="file" class="form-control-file" name="import_file" required>

                                    @if($errors->has('import_file'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('import_file') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        Import
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        {{ trans('cruds.estimation.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <form id="filterform">
            <div class="row mb-5">
                <div class="col row">
                    <div class="col-6">
                        <div class="form-group mb-0">
                            <label class="small mb-0" for="semester_id">{{ trans('cruds.salesOrder.fields.semester') }}</label>
                            <select class="form-control select2 {{ $errors->has('semester') ? 'is-invalid' : '' }}" name="semester_id" id="semester_id">
                                @foreach($semesters as $id => $entry)
                                    <option value="{{ $id }}" {{ old('semester_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group mb-0">
                            <label class="small mb-0" for="salesperson_id">{{ trans('cruds.salesOrder.fields.salesperson') }}</label>
                            <select class="form-control select2 {{ $errors->has('salesperson') ? 'is-invalid' : '' }}" name="salesperson_id" id="salesperson_id">
                                @foreach($salespeople as $id => $entry)
                                    <option value="{{ $id }}" {{ old('salesperson_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-auto align-self-end">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Estimation">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.estimation.fields.no_estimasi') }}
                    </th>
                    <th>
                        {{ trans('cruds.estimation.fields.date') }}
                    </th>
                    <th>
                        {{ trans('cruds.estimation.fields.semester') }}
                    </th>
                    <th>
                        {{ trans('cruds.estimation.fields.salesperson') }}
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
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

  let dtOverrideGlobals = {
    buttons: dtButtons,
    processing: true,
    serverSide: true,
    retrieve: true,
    aaSorting: [],
    ajax: {
        url: "{{ route('admin.estimations.index') }}",
        data: function(data) {
            data.salesperson = $('#salesperson_id').val(),
            data.semester = $('#semester_id').val()
        }
    },
    columns: [
        { data: 'placeholder', name: 'placeholder' },
        { data: 'no_estimasi', name: 'no_estimasi', class: 'text-center' },
        { data: 'date', name: 'date', class: 'text-center' },
        { data: 'semester_name', name: 'semester.name', class: 'text-center' },
        { data: 'salesperson_name', name: 'salesperson.name', class: 'text-center' },
        { data: 'actions', name: '{{ trans('global.actions') }}', class: 'text-center' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 50,
  };
  let table = $('.datatable-Estimation').DataTable(dtOverrideGlobals);
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
