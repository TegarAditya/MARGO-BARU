@extends('layouts.admin')
@section('content')
@can('production_estimation_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.production-estimations.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.productionEstimation.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.productionEstimation.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-ProductionEstimation">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.productionEstimation.fields.product') }}
                    </th>
                    <th>
                        {{ trans('cruds.productionEstimation.fields.estimasi') }}
                    </th>
                    <th>
                        {{ trans('cruds.productionEstimation.fields.isi') }}
                    </th>
                    <th>
                        {{ trans('cruds.productionEstimation.fields.cover') }}
                    </th>
                    <th>
                        {{ trans('cruds.productionEstimation.fields.finishing') }}
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
@can('production_estimation_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.production-estimations.massDestroy') }}",
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
    ajax: "{{ route('admin.production-estimations.index') }}",
    columns: [
        { data: 'placeholder', name: 'placeholder' },
        { data: 'product_code', name: 'product.code' },
        { data: 'estimasi', name: 'estimasi', class: 'text-right' },
        { data: 'isi', name: 'isi', class: 'text-right' },
        { data: 'cover', name: 'cover', class: 'text-right' },
        { data: 'finishing', name: 'finishing', class: 'text-right' },
        { data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 50,
  };
  let table = $('.datatable-ProductionEstimation').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });

});

</script>
@endsection
