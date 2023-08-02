@extends('layouts.admin')
@section('content')
@can('bill_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.bills.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.bill.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.bill.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Bill">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.bill.fields.semester') }}
                    </th>
                    <th>
                        {{ trans('cruds.bill.fields.salesperson') }}
                    </th>
                    <th>
                        {{ trans('cruds.bill.fields.saldo_awal') }}
                    </th>
                    <th>
                        {{ trans('cruds.bill.fields.jual') }}
                    </th>
                    <th>
                        {{ trans('cruds.bill.fields.diskon') }}
                    </th>
                    <th>
                        {{ trans('cruds.bill.fields.retur') }}
                    </th>
                    <th>
                        {{ trans('cruds.bill.fields.bayar') }}
                    </th>
                    <th>
                        {{ trans('cruds.bill.fields.potongan') }}
                    </th>
                    <th>
                        {{ trans('cruds.bill.fields.saldo_akhir') }}
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
    ajax: "{{ route('admin.bills.index') }}",
    columns: [
        { data: 'placeholder', name: 'placeholder' },
        { data: 'semester_name', name: 'semester.name' },
        { data: 'salesperson_name', name: 'salesperson.name' },
        { data: 'saldo_awal', name: 'saldo_awal' },
        { data: 'jual', name: 'jual' },
        { data: 'diskon', name: 'diskon' },
        { data: 'retur', name: 'retur' },
        { data: 'bayar', name: 'bayar' },
        { data: 'potongan', name: 'potongan' },
        { data: 'saldo_akhir', name: 'saldo_akhir' },
        { data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 4, 'desc' ]],
    pageLength: 50,
  };
  let table = $('.datatable-Bill').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection