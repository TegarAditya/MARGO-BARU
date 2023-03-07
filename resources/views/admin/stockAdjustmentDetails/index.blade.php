@extends('layouts.admin')
@section('content')
@can('stock_adjustment_detail_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.stock-adjustment-details.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.stockAdjustmentDetail.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.stockAdjustmentDetail.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-StockAdjustmentDetail">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.stockAdjustmentDetail.fields.product') }}
                    </th>
                    <th>
                        {{ trans('cruds.stockAdjustmentDetail.fields.material') }}
                    </th>
                    <th>
                        {{ trans('cruds.stockAdjustmentDetail.fields.stock_adjustment') }}
                    </th>
                    <th>
                        {{ trans('cruds.stockAdjustment.fields.reason') }}
                    </th>
                    <th>
                        {{ trans('cruds.stockAdjustmentDetail.fields.quantity') }}
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
@can('stock_adjustment_detail_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.stock-adjustment-details.massDestroy') }}",
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
    ajax: "{{ route('admin.stock-adjustment-details.index') }}",
    columns: [
      { data: 'placeholder', name: 'placeholder' },
{ data: 'product_code', name: 'product.code' },
{ data: 'material_code', name: 'material.code' },
{ data: 'stock_adjustment_reason', name: 'stock_adjustment.reason' },
{ data: 'stock_adjustment.reason', name: 'stock_adjustment.reason' },
{ data: 'quantity', name: 'quantity' },
{ data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 25,
  };
  let table = $('.datatable-StockAdjustmentDetail').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection