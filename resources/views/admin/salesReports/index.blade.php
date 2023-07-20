@extends('layouts.admin')
@section('content')
@can('sales_report_create')
<div style="margin-bottom: 10px;" class="row">
    <div class="col-lg-12">
        <form action="{{ route('admin.sales-reports.generate') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-danger">Generate Saldo</button>
        </form>
    </div>
</div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.salesReport.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-SalesReport">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th class="text-center">
                        {{ trans('cruds.salesReport.fields.code') }}
                    </th>
                    <th class="text-center">
                        {{ trans('cruds.salesReport.fields.periode') }}
                    </th>
                    <th class="text-center">
                        {{ trans('cruds.salesReport.fields.salesperson') }}
                    </th>
                    <th class="text-center">
                        {{ trans('cruds.salesReport.fields.start_date') }}
                    </th>
                    <th class="text-center">
                        {{ trans('cruds.salesReport.fields.end_date') }}
                    </th>
                    <th class="text-center">
                        {{ trans('cruds.salesReport.fields.saldo_awal') }}
                    </th>
                    <th class="text-center">
                        {{ trans('cruds.salesReport.fields.debet') }}
                    </th>
                    <th class="text-center">
                        {{ trans('cruds.salesReport.fields.kredit') }}
                    </th>
                    <th class="text-center">
                        {{ trans('cruds.salesReport.fields.saldo_akhir') }}
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
    ajax: "{{ route('admin.sales-reports.index') }}",
    columns: [
        { data: 'placeholder', name: 'placeholder' },
        { data: 'code', name: 'code', class: 'text-center' },
        { data: 'periode', name: 'periode' },
        { data: 'salesperson_name', name: 'salesperson.name', class: 'text-center' },
        { data: 'start_date', name: 'start_date', class: 'text-center' },
        { data: 'end_date', name: 'end_date', class: 'text-center' },
        { data: 'saldo_awal', name: 'saldo_awal', class: 'text-right' },
        { data: 'debet', name: 'debet', class: 'text-right' },
        { data: 'kredit', name: 'kredit', class: 'text-right' },
        { data: 'saldo_akhir', name: 'saldo_akhir', class: 'text-right' },
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 25,
  };
  let table = $('.datatable-SalesReport').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection