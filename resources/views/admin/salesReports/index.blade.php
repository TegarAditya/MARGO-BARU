@extends('layouts.admin')
@section('content')
@can('sales_report_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.sales-reports.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.salesReport.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.salesReport.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-SalesReport">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.salesReport.fields.code') }}
                    </th>
                    <th>
                        {{ trans('cruds.salesReport.fields.periode') }}
                    </th>
                    <th>
                        {{ trans('cruds.salesReport.fields.salesperson') }}
                    </th>
                    <th>
                        {{ trans('cruds.salesReport.fields.start_date') }}
                    </th>
                    <th>
                        {{ trans('cruds.salesReport.fields.end_date') }}
                    </th>
                    <th>
                        {{ trans('cruds.salesReport.fields.type') }}
                    </th>
                    <th>
                        {{ trans('cruds.salesReport.fields.saldo_awal') }}
                    </th>
                    <th>
                        {{ trans('cruds.salesReport.fields.debet') }}
                    </th>
                    <th>
                        {{ trans('cruds.salesReport.fields.kredit') }}
                    </th>
                    <th>
                        {{ trans('cruds.salesReport.fields.saldo_akhir') }}
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
@can('sales_report_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.sales-reports.massDestroy') }}",
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
    ajax: "{{ route('admin.sales-reports.index') }}",
    columns: [
      { data: 'placeholder', name: 'placeholder' },
{ data: 'code', name: 'code' },
{ data: 'periode', name: 'periode' },
{ data: 'salesperson_name', name: 'salesperson.name' },
{ data: 'start_date', name: 'start_date' },
{ data: 'end_date', name: 'end_date' },
{ data: 'type', name: 'type' },
{ data: 'saldo_awal', name: 'saldo_awal' },
{ data: 'debet', name: 'debet' },
{ data: 'kredit', name: 'kredit' },
{ data: 'saldo_akhir', name: 'saldo_akhir' },
{ data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 4, 'desc' ]],
    pageLength: 25,
  };
  let table = $('.datatable-SalesReport').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection