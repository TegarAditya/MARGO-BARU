@extends('layouts.admin')
@section('content')
@can('invoice_item_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.invoice-items.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.invoiceItem.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.invoiceItem.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-InvoiceItem">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.invoiceItem.fields.invoice') }}
                    </th>
                    <th>
                        {{ trans('cruds.invoiceItem.fields.salesperson') }}
                    </th>
                    <th>
                        {{ trans('cruds.invoiceItem.fields.product') }}
                    </th>
                    <th>
                        {{ trans('cruds.invoiceItem.fields.quantity') }}
                    </th>
                    <th>
                        {{ trans('cruds.invoiceItem.fields.price_unit') }}
                    </th>
                    <th>
                        {{ trans('cruds.invoiceItem.fields.discount') }}
                    </th>
                    <th>
                        {{ trans('cruds.invoiceItem.fields.price') }}
                    </th>
                    <th>
                        {{ trans('cruds.invoiceItem.fields.total') }}
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
@can('invoice_item_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.invoice-items.massDestroy') }}",
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
    ajax: "{{ route('admin.invoice-items.index') }}",
    columns: [
      { data: 'placeholder', name: 'placeholder' },
{ data: 'invoice_no_faktur', name: 'invoice.no_faktur' },
{ data: 'salesperson_name', name: 'salesperson.name' },
{ data: 'product_code', name: 'product.code' },
{ data: 'quantity', name: 'quantity' },
{ data: 'price_unit', name: 'price_unit' },
{ data: 'discount', name: 'discount' },
{ data: 'price', name: 'price' },
{ data: 'total', name: 'total' },
{ data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 50,
  };
  let table = $('.datatable-InvoiceItem').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection