@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('cruds.estimationMovement.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-EstimationMovement">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.estimationMovement.fields.movement_date') }}
                    </th>
                    <th>
                        {{ trans('cruds.estimationMovement.fields.movement_type') }}
                    </th>
                    <th>
                        {{ trans('cruds.estimationMovement.fields.product') }}
                    </th>
                    <th>
                        {{ trans('cruds.estimationMovement.fields.type') }}
                    </th>
                    <th>
                        Quantity
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

  let dtOverrideGlobals = {
    buttons: dtButtons,
    processing: true,
    serverSide: true,
    retrieve: true,
    aaSorting: [],
    ajax: "{{ route('admin.estimation-movements.index') }}",
    columns: [
        { data: 'placeholder', name: 'placeholder' },
        { data: 'movement_date', name: 'movement_date', class: 'text-center' },
        { data: 'movement_type', name: 'movement_type', class: 'text-center' },
        { data: 'product_code', name: 'product.code' },
        { data: 'type', name: 'type', class: 'text-center' },
        { data: 'quantity', name: 'quantity', class: 'text-right' },
        { data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    // order: [[ 1, 'desc' ]],
    pageLength: 50,
  };
  let table = $('.datatable-EstimationMovement').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });

});

</script>
@endsection
