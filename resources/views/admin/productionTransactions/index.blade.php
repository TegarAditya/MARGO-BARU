@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
        {{ trans('cruds.productionTransaction.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-ProductionTransaction">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.productionTransaction.fields.date') }}
                    </th>
                    <th>
                        {{ trans('cruds.productionTransaction.fields.description') }}
                    </th>
                    <th>
                        {{ trans('cruds.productionTransaction.fields.vendor') }}
                    </th>
                    <th>
                        {{ trans('cruds.productionTransaction.fields.semester') }}
                    </th>
                    <th>
                        {{ trans('cruds.productionTransaction.fields.type') }}
                    </th>
                    <th>
                        {{ trans('cruds.productionTransaction.fields.reference') }}
                    </th>
                    <th>
                        {{ trans('cruds.productionTransaction.fields.reference_no') }}
                    </th>
                    <th>
                        {{ trans('cruds.productionTransaction.fields.transaction_date') }}
                    </th>
                    <th>
                        {{ trans('cruds.productionTransaction.fields.amount') }}
                    </th>
                    <th>
                        {{ trans('cruds.productionTransaction.fields.category') }}
                    </th>
                    <th>
                        {{ trans('cruds.productionTransaction.fields.status') }}
                    </th>
                    <th>
                        {{ trans('cruds.productionTransaction.fields.reversal_of') }}
                    </th>
                    <th>
                        {{ trans('cruds.productionTransaction.fields.created_by') }}
                    </th>
                    <th>
                        {{ trans('cruds.productionTransaction.fields.updated_by') }}
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
    ajax: "{{ route('admin.production-transactions.index') }}",
    columns: [
        { data: 'placeholder', name: 'placeholder' },
        { data: 'date', name: 'date' },
        { data: 'description', name: 'description' },
        { data: 'vendor_name', name: 'vendor.name' },
        { data: 'semester_name', name: 'semester.name' },
        { data: 'type', name: 'type' },
        { data: 'reference_no_faktur', name: 'reference.no_faktur' },
        { data: 'reference_no', name: 'reference_no' },
        { data: 'transaction_date', name: 'transaction_date' },
        { data: 'amount', name: 'amount' },
        { data: 'category', name: 'category' },
        { data: 'status', name: 'status' },
        { data: 'reversal_of_description', name: 'reversal_of.description' },
        { data: 'created_by_name', name: 'created_by.name' },
        { data: 'updated_by_name', name: 'updated_by.name' },
        { data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 50,
  };
  let table = $('.datatable-ProductionTransaction').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection