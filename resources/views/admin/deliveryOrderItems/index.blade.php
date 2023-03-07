@extends('layouts.admin')
@section('content')
@can('delivery_order_item_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.delivery-order-items.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.deliveryOrderItem.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.deliveryOrderItem.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-DeliveryOrderItem">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.deliveryOrderItem.fields.semester') }}
                    </th>
                    <th>
                        {{ trans('cruds.deliveryOrderItem.fields.salesperson') }}
                    </th>
                    <th>
                        {{ trans('cruds.deliveryOrderItem.fields.sales_order') }}
                    </th>
                    <th>
                        {{ trans('cruds.deliveryOrderItem.fields.delivery_order') }}
                    </th>
                    <th>
                        {{ trans('cruds.deliveryOrderItem.fields.product') }}
                    </th>
                    <th>
                        {{ trans('cruds.deliveryOrderItem.fields.quantity') }}
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
@can('delivery_order_item_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.delivery-order-items.massDestroy') }}",
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
    ajax: "{{ route('admin.delivery-order-items.index') }}",
    columns: [
      { data: 'placeholder', name: 'placeholder' },
{ data: 'semester_name', name: 'semester.name' },
{ data: 'salesperson_name', name: 'salesperson.name' },
{ data: 'sales_order_quantity', name: 'sales_order.quantity' },
{ data: 'delivery_order_no_suratjalan', name: 'delivery_order.no_suratjalan' },
{ data: 'product_code', name: 'product.code' },
{ data: 'quantity', name: 'quantity' },
{ data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 4, 'desc' ]],
    pageLength: 50,
  };
  let table = $('.datatable-DeliveryOrderItem').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection