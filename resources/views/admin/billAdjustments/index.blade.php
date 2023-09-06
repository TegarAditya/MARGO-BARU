@extends('layouts.admin')
@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1 class="m-0 bold">Billing Adjustment</h1>
    </div>
</div>
@can('bill_adjustment_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.bill-adjustments.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.billAdjustment.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.billAdjustment.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-BillAdjustment">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.billAdjustment.fields.no_adjustment') }}
                    </th>
                    <th>
                        {{ trans('cruds.billAdjustment.fields.date') }}
                    </th>
                    <th>
                        {{ trans('cruds.billAdjustment.fields.salesperson') }}
                    </th>
                    <th>
                        {{ trans('cruds.billAdjustment.fields.semester') }}
                    </th>
                    <th>
                        {{ trans('cruds.billAdjustment.fields.amount') }}
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
    ajax: "{{ route('admin.bill-adjustments.index') }}",
    columns: [
        { data: 'placeholder', name: 'placeholder' },
        { data: 'no_adjustment', name: 'no_adjustment', class: 'text-center' },
        { data: 'date', name: 'date', class: 'text-center' },
        { data: 'salesperson_name', name: 'salesperson.name', class: 'text-center' },
        { data: 'semester_name', name: 'semester.name', class: 'text-center' },
        { data: 'amount', name: 'amount', class: 'text-center' },
        { data: 'actions', name: '{{ trans('global.actions') }}', class: 'text-center' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 50,
  };
  let table = $('.datatable-BillAdjustment').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection