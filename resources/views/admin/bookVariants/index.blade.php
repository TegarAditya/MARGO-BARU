@extends('layouts.admin')
@section('content')
@can('book_variant_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.book-variants.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.bookVariant.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.bookVariant.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-BookVariant">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.bookVariant.fields.code') }}
                    </th>
                    <th>
                        {{ trans('cruds.bookVariant.fields.type') }}
                    </th>
                    <th>
                        {{ trans('cruds.bookVariant.fields.jenjang') }}
                    </th>
                    <th>
                        {{ trans('cruds.bookVariant.fields.semester') }}
                    </th>
                    <th>
                        {{ trans('cruds.bookVariant.fields.kurikulum') }}
                    </th>
                    <th>
                        {{ trans('cruds.bookVariant.fields.halaman') }}
                    </th>
                    <th>
                        {{ trans('cruds.bookVariant.fields.stock') }}
                    </th>
                    <th>
                        {{ trans('cruds.bookVariant.fields.price') }}
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
@can('book_variant_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.book-variants.massDestroy') }}",
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
    ajax: "{{ route('admin.book-variants.index') }}",
    columns: [
      { data: 'placeholder', name: 'placeholder' },
{ data: 'code', name: 'code' },
{ data: 'type', name: 'type' },
{ data: 'jenjang_code', name: 'jenjang.code' },
{ data: 'semester_name', name: 'semester.name' },
{ data: 'kurikulum_code', name: 'kurikulum.code' },
{ data: 'halaman_name', name: 'halaman.name' },
{ data: 'stock', name: 'stock' },
{ data: 'price', name: 'price' },
{ data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 50,
  };
  let table = $('.datatable-BookVariant').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection