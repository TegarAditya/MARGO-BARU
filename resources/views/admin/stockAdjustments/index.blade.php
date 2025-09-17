@extends('layouts.admin')
@section('content')
@can('stock_adjustment_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.stock-adjustments.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.stockAdjustment.title_singular') }} Buku
            </a>
            <a class="btn btn-warning" href="{{ route('admin.stock-adjustments.create', ['type' => 'material']) }}">
                {{ trans('global.add') }} {{ trans('cruds.stockAdjustment.title_singular') }} Material
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

                        <form class="form-horizontal" method="POST" action="{{ route('admin.stock-adjustments.import') }}" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label class="required" for="date">{{ trans('cruds.stockAdjustment.fields.date') }}</label>
                                        <input class="form-control date {{ $errors->has('date') ? 'is-invalid' : '' }}" type="text" name="date" id="date" value="{{ old('date', $today) }}" required>
                                        @if($errors->has('date'))
                                            <span class="text-danger">{{ $errors->first('date') }}</span>
                                        @endif
                                        <span class="help-block">{{ trans('cruds.stockAdjustment.fields.date_helper') }}</span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label class="required">{{ trans('cruds.stockAdjustment.fields.operation') }}</label>
                                        <select class="form-control {{ $errors->has('operation') ? 'is-invalid' : '' }}" name="operation" id="operation" required>
                                            <option value disabled {{ old('operation', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                                            @foreach(App\Models\StockAdjustment::OPERATION_SELECT as $key => $label)
                                                <option value="{{ $key }}" {{ old('operation', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @if($errors->has('operation'))
                                            <span class="text-danger">{{ $errors->first('operation') }}</span>
                                        @endif
                                        <span class="help-block">{{ trans('cruds.stockAdjustment.fields.operation_helper') }}</span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label class="required">{{ trans('cruds.stockAdjustment.fields.type') }}</label>
                                        <select class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type" id="type" required>
                                            <option value disabled {{ old('type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                                            @foreach(App\Models\StockAdjustment::TYPE_SELECT as $key => $label)
                                                <option value="{{ $key }}" {{ old('type', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @if($errors->has('type'))
                                            <span class="text-danger">{{ $errors->first('type') }}</span>
                                        @endif
                                        <span class="help-block">{{ trans('cruds.stockAdjustment.fields.type_helper') }}</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="required" for="reason">{{ trans('cruds.stockAdjustment.fields.reason') }}</label>
                                        <input class="form-control {{ $errors->has('reason') ? 'is-invalid' : '' }}" type="text" name="reason" id="reason" value="{{ old('reason', '') }}" required>
                                        @if($errors->has('reason'))
                                            <span class="text-danger">{{ $errors->first('reason') }}</span>
                                        @endif
                                        <span class="help-block">{{ trans('cruds.stockAdjustment.fields.reason_helper') }}</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="note">{{ trans('cruds.stockAdjustment.fields.note') }}</label>
                                        <textarea class="form-control {{ $errors->has('note') ? 'is-invalid' : '' }}" name="note" id="note">{{ old('note') }}</textarea>
                                        @if($errors->has('note'))
                                            <span class="text-danger">{{ $errors->first('note') }}</span>
                                        @endif
                                        <span class="help-block">{{ trans('cruds.stockAdjustment.fields.note_helper') }}</span>
                                    </div>
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
        {{ trans('cruds.stockAdjustment.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-StockAdjustment">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th width="15%">
                        {{ trans('cruds.stockAdjustment.fields.operation') }}
                    </th>
                    <th>
                        {{ trans('cruds.stockAdjustment.fields.date') }}
                    </th>
                    <th>
                        {{ trans('cruds.stockAdjustment.fields.type') }}
                    </th>
                    <th>
                        {{ trans('cruds.stockAdjustment.fields.reason') }}
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
    ajax: "{{ route('admin.stock-adjustments.index') }}",
    columns: [
        { data: 'placeholder', name: 'placeholder' },
        { data: 'operation', name: 'operation', class: 'text-center' },
        { data: 'date', name: 'date', class: 'text-center' },
        { data: 'type', name: 'type', class: 'text-center' },
        { data: 'reason', name: 'reason' },
        { data: 'actions', name: '{{ trans('global.actions') }}', class: 'text-center' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 25,
  };
  let table = $('.datatable-StockAdjustment').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });

});
</script>
@endsection
