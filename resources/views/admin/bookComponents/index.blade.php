@extends('layouts.admin')
@section('content')
@can('book_component_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.book-components.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.bookComponent.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.bookComponent.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-BookComponent">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.bookComponent.fields.code') }}
                    </th>
                    <th>
                        {{ trans('cruds.bookComponent.fields.name') }}
                    </th>
                    <th>
                        {{ trans('cruds.bookComponent.fields.description') }}
                    </th>
                    <th>
                        {{ trans('cruds.bookComponent.fields.type') }}
                    </th>
                    <th>
                        {{ trans('cruds.bookComponent.fields.jenjang') }}
                    </th>
                    <th>
                        {{ trans('cruds.bookComponent.fields.kurikulum') }}
                    </th>
                    <th>
                        {{ trans('cruds.bookComponent.fields.isi') }}
                    </th>
                    <th>
                        {{ trans('cruds.bookComponent.fields.cover') }}
                    </th>
                    <th>
                        {{ trans('cruds.bookComponent.fields.mapel') }}
                    </th>
                    <th>
                        {{ trans('cruds.bookComponent.fields.kelas') }}
                    </th>
                    <th>
                        {{ trans('cruds.bookComponent.fields.halaman') }}
                    </th>
                    <th>
                        {{ trans('cruds.bookComponent.fields.semester') }}
                    </th>
                    <th>
                        {{ trans('cruds.bookComponent.fields.warehouse') }}
                    </th>
                    <th>
                        {{ trans('cruds.bookComponent.fields.stock') }}
                    </th>
                    <th>
                        {{ trans('cruds.bookComponent.fields.unit') }}
                    </th>
                    <th>
                        {{ trans('cruds.bookComponent.fields.price') }}
                    </th>
                    <th>
                        {{ trans('cruds.bookComponent.fields.cost') }}
                    </th>
                    <th>
                        {{ trans('cruds.bookComponent.fields.components') }}
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
@can('book_component_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.book-components.massDestroy') }}",
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
    ajax: "{{ route('admin.book-components.index') }}",
    columns: [
      { data: 'placeholder', name: 'placeholder' },
{ data: 'code', name: 'code' },
{ data: 'name', name: 'name' },
{ data: 'description', name: 'description' },
{ data: 'type', name: 'type' },
{ data: 'jenjang_code', name: 'jenjang.code' },
{ data: 'kurikulum_code', name: 'kurikulum.code' },
{ data: 'isi_code', name: 'isi.code' },
{ data: 'cover_code', name: 'cover.code' },
{ data: 'mapel_name', name: 'mapel.name' },
{ data: 'kelas_code', name: 'kelas.code' },
{ data: 'halaman_code', name: 'halaman.code' },
{ data: 'semester_name', name: 'semester.name' },
{ data: 'warehouse_code', name: 'warehouse.code' },
{ data: 'stock', name: 'stock' },
{ data: 'unit_code', name: 'unit.code' },
{ data: 'price', name: 'price' },
{ data: 'cost', name: 'cost' },
{ data: 'components', name: 'components.code' },
{ data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 50,
  };
  let table = $('.datatable-BookComponent').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection