@extends('layouts.admin')
@section('content')
@can('cetak_item_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.cetak-items.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.cetakItem.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.cetakItem.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-CetakItem">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.cetakItem.fields.semester') }}
                    </th>
                    <th>
                        {{ trans('cruds.cetakItem.fields.product') }}
                    </th>
                    <th>
                        {{ trans('cruds.cetakItem.fields.halaman') }}
                    </th>
                    <th>
                        {{ trans('cruds.cetakItem.fields.quantity') }}
                    </th>
                    <th>
                        {{ trans('cruds.cetakItem.fields.cost') }}
                    </th>
                    <th>
                        {{ trans('cruds.cetakItem.fields.plate') }}
                    </th>
                    <th>
                        {{ trans('cruds.cetakItem.fields.plate_cost') }}
                    </th>
                    <th>
                        {{ trans('cruds.cetakItem.fields.paper') }}
                    </th>
                    <th>
                        {{ trans('cruds.cetakItem.fields.paper_cost') }}
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
@can('cetak_item_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.cetak-items.massDestroy') }}",
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
    ajax: "{{ route('admin.cetak-items.index') }}",
    columns: [
      { data: 'placeholder', name: 'placeholder' },
{ data: 'semester_name', name: 'semester.name' },
{ data: 'product_code', name: 'product.code' },
{ data: 'halaman_name', name: 'halaman.name' },
{ data: 'quantity', name: 'quantity' },
{ data: 'cost', name: 'cost' },
{ data: 'plate_code', name: 'plate.code' },
{ data: 'plate_cost', name: 'plate_cost' },
{ data: 'paper_code', name: 'paper.code' },
{ data: 'paper_cost', name: 'paper_cost' },
{ data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 2, 'desc' ]],
    pageLength: 50,
  };
  let table = $('.datatable-CetakItem').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection