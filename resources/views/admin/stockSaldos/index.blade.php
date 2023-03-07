@extends('layouts.admin')
@section('content')
@can('stock_saldo_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.stock-saldos.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.stockSaldo.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.stockSaldo.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-StockSaldo">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.stockSaldo.fields.code') }}
                    </th>
                    <th>
                        {{ trans('cruds.stockSaldo.fields.product') }}
                    </th>
                    <th>
                        {{ trans('cruds.stockSaldo.fields.material') }}
                    </th>
                    <th>
                        {{ trans('cruds.stockSaldo.fields.periode') }}
                    </th>
                    <th>
                        {{ trans('cruds.stockSaldo.fields.start_date') }}
                    </th>
                    <th>
                        {{ trans('cruds.stockSaldo.fields.end_date') }}
                    </th>
                    <th>
                        {{ trans('cruds.stockSaldo.fields.qty_awal') }}
                    </th>
                    <th>
                        {{ trans('cruds.stockSaldo.fields.in') }}
                    </th>
                    <th>
                        {{ trans('cruds.stockSaldo.fields.out') }}
                    </th>
                    <th>
                        {{ trans('cruds.stockSaldo.fields.qty_akhir') }}
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
@can('stock_saldo_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.stock-saldos.massDestroy') }}",
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
    ajax: "{{ route('admin.stock-saldos.index') }}",
    columns: [
      { data: 'placeholder', name: 'placeholder' },
{ data: 'code', name: 'code' },
{ data: 'product_code', name: 'product.code' },
{ data: 'material_code', name: 'material.code' },
{ data: 'periode', name: 'periode' },
{ data: 'start_date', name: 'start_date' },
{ data: 'end_date', name: 'end_date' },
{ data: 'qty_awal', name: 'qty_awal' },
{ data: 'in', name: 'in' },
{ data: 'out', name: 'out' },
{ data: 'qty_akhir', name: 'qty_akhir' },
{ data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 50,
  };
  let table = $('.datatable-StockSaldo').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection