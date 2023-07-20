@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
        {{ trans('cruds.transaction.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Transaction">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.transaction.fields.date') }}
                    </th>
                    <th>
                        {{ trans('cruds.transaction.fields.description') }}
                    </th>
                    <th>
                        {{ trans('cruds.transaction.fields.salesperson') }}
                    </th>
                    <th>
                        {{ trans('cruds.transaction.fields.semester') }}
                    </th>
                    <th>
                        {{ trans('cruds.transaction.fields.type') }}
                    </th>
                    <th>
                        {{ trans('cruds.transaction.fields.reference') }}
                    </th>
                    <th>
                        {{ trans('cruds.transaction.fields.reference_no') }}
                    </th>
                    <th>
                        {{ trans('cruds.transaction.fields.amount') }}
                    </th>
                    <th>
                        {{ trans('cruds.transaction.fields.category') }}
                    </th>
                    <th>
                        {{ trans('cruds.transaction.fields.status') }}
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
  let dtOverrideGlobals = {
    processing: true,
    serverSide: true,
    retrieve: true,
    aaSorting: [],
    ajax: "{{ route('admin.transactions.index') }}",
    columns: [
        { data: 'placeholder', name: 'placeholder' },
        { data: 'date', name: 'date' },
        { data: 'description', name: 'description' },
        { data: 'salesperson_name', name: 'salesperson.name' },
        { data: 'semester_name', name: 'semester.name' },
        { data: 'type', name: 'type' },
        { data: 'reference_no_faktur', name: 'reference.no_faktur' },
        { data: 'reference_no', name: 'reference_no' },
        { data: 'amount', name: 'amount' },
        { data: 'category', name: 'category' },
        { data: 'status', name: 'status' },
        { data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 50,
  };
  let table = $('.datatable-Transaction').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection