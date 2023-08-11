@extends('layouts.admin')
@section('content')
@can('aquarium_access')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.delivery-plates.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.deliveryPlate.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.deliveryPlate.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-DeliveryPlate">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.deliveryPlate.fields.no_suratjalan') }}
                    </th>
                    <th>
                        {{ trans('cruds.deliveryPlate.fields.date') }}
                    </th>
                    <th>
                        Vendor / Customer
                    </th>
                    <th>
                        {{ trans('cruds.deliveryPlate.fields.note') }}
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
    ajax: "{{ route('admin.delivery-plates.index') }}",
    columns: [
        { data: 'placeholder', name: 'placeholder' },
        { data: 'no_suratjalan', name: 'no_suratjalan', class: 'text-center' },
        { data: 'date', name: 'date', class: 'text-center' },
        { data: 'customer', name: 'customer', class: 'text-center' },
        { data: 'note', name: 'note' },
        { data: 'actions', name: '{{ trans('global.actions') }}', class: 'text-center' }
    ],
    orderCellsTop: true,
    // order: [[ 1, 'desc' ]],
    pageLength: 50,
  };
  let table = $('.datatable-DeliveryPlate').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });

});

</script>
@endsection
