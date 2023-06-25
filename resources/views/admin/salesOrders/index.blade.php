@extends('layouts.admin')
@section('content')
@can('sales_order_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.sales-orders.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.salesOrder.title_singular') }}
            </a>
            <a class="btn btn-danger" href="{{ route('admin.sales-orders.templateImport') }}">
                Template Import
            </a>
            <button class="btn btn-primary" data-toggle="modal" data-target="#importModal">
                Import
            </button>
            @include('csvImport.import_modal', ['model' => 'SalesOrder', 'route' => 'admin.sales-orders.import'])
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.salesOrder.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <form id="filterform">
            <div class="row mb-5">
                <div class="col row">
                    <div class="col-4">
                        <div class="form-group mb-0">
                            <label class="small mb-0" for="semester_id">{{ trans('cruds.salesOrder.fields.semester') }}</label>
                            <select class="form-control select2 {{ $errors->has('semester') ? 'is-invalid' : '' }}" name="semester_id" id="semester_id">
                                @foreach($semesters as $id => $entry)
                                    <option value="{{ $id }}" {{ old('semester_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="form-group mb-0">
                            <label class="small mb-0" for="salesperson_id">{{ trans('cruds.salesOrder.fields.salesperson') }}</label>
                            <select class="form-control select2 {{ $errors->has('salesperson') ? 'is-invalid' : '' }}" name="salesperson_id" id="salesperson_id">
                                @foreach($salespeople as $id => $entry)
                                    <option value="{{ $id }}" {{ old('salesperson_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group mb-0">
                            <label class="small mb-0" for="payment_type">{{ trans('cruds.salesOrder.fields.payment_type') }}</label>
                            <select class="form-control {{ $errors->has('payment_type') ? 'is-invalid' : '' }}" name="payment_type" id="payment_type">
                                <option value disabled {{ old('payment_type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                                @foreach(App\Models\SalesOrder::PAYMENT_TYPE_SELECT as $key => $label)
                                    <option value="{{ $key }}" {{ old('payment_type', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
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
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-SalesOrder">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.salesOrder.fields.semester') }}
                    </th>
                    <th>
                        {{ trans('cruds.salesOrder.fields.salesperson') }}
                    </th>
                    <th>
                        Payment Type
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
@can('sales_order_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.sales-orders.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).data(), function (entry) {
          return entry.id
      });

      if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')

        return
      }

      if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({
          headers: {'x-csrf-token': _token},
          method: 'POST',
          url: config.url,
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  dtButtons.push(deleteButton)
@endcan

  let dtOverrideGlobals = {
    buttons: dtButtons,
    processing: true,
    serverSide: true,
    retrieve: true,
    aaSorting: [],
    ajax: {
        url: "{{ route('admin.sales-orders.index') }}",
        data: function(data) {
            data.salesperson = $('#salesperson_id').val()
            data.semester = $('#semester_id').val()
            data.payment_type = $('#payment_type').val()
        }
    },
    columns: [
        { data: 'placeholder', name: 'placeholder' },
        { data: 'semester_name', name: 'semester.name', class: 'text-center' },
        { data: 'salesperson_name', name: 'salesperson.name', class: 'text-center' },
        { data: 'payment_type', name: 'payment_type', class: 'text-center' },
        { data: 'actions', name: '{{ trans('global.actions') }}', class: 'text-center' }
    ],
    orderCellsTop: true,
    order: [[ 2, 'desc' ]],
    pageLength: 25,
  };
  let table = $('.datatable-SalesOrder').DataTable(dtOverrideGlobals);
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
