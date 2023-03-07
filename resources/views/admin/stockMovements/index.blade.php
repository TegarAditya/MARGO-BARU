@extends('layouts.admin')
@section('content')
@can('stock_movement_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.stock-movements.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.stockMovement.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.stockMovement.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-StockMovement">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.stockMovement.fields.movement_date') }}
                    </th>
                    <th>
                        {{ trans('cruds.stockMovement.fields.movement_type') }}
                    </th>
                    <th>
                        {{ trans('cruds.stockMovement.fields.product') }}
                    </th>
                    <th>
                        {{ trans('cruds.stockMovement.fields.material') }}
                    </th>
                    <th>
                        {{ trans('cruds.stockMovement.fields.quantity') }}
                    </th>
                    <th>
                        {{ trans('cruds.stockMovement.fields.transaction_type') }}
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
@can('stock_movement_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.stock-movements.massDestroy') }}",
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
    ajax: "{{ route('admin.stock-movements.index') }}",
    columns: [
      { data: 'placeholder', name: 'placeholder' },
{ data: 'movement_date', name: 'movement_date' },
{ data: 'movement_type', name: 'movement_type' },
{ data: 'product_code', name: 'product.code' },
{ data: 'material_code', name: 'material.code' },
{ data: 'quantity', name: 'quantity' },
{ data: 'transaction_type', name: 'transaction_type' },
{ data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 50,
  };
  let table = $('.datatable-StockMovement').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection