@extends('layouts.admin')
@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1 class="m-0">Pembayaran Vendor</h1>
    </div>
</div>
@can('production_payment_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.production-payments.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.productionPayment.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.productionPayment.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-ProductionPayment">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.productionPayment.fields.no_payment') }}
                    </th>
                    <th>
                        {{ trans('cruds.productionPayment.fields.date') }}
                    </th>
                    <th>
                        {{ trans('cruds.productionPayment.fields.vendor') }}
                    </th>
                    <th>
                        {{ trans('cruds.productionPayment.fields.payment_method') }}
                    </th>
                    <th>
                        {{ trans('cruds.productionPayment.fields.nominal') }}
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
    ajax: "{{ route('admin.production-payments.index') }}",
    columns: [
        { data: 'placeholder', name: 'placeholder' },
        { data: 'no_payment', name: 'no_payment', class: 'text-center' },
        { data: 'date', name: 'date', class: 'text-center' },
        { data: 'vendor_name', name: 'vendor.name', class: 'text-center' },
        { data: 'payment_method', name: 'payment_method', class: 'text-center' },
        { data: 'nominal', name: 'nominal', class: 'text-right' },
        { data: 'actions', name: '{{ trans('global.actions') }}', class: 'text-center'  }
    ],
    orderCellsTop: true,
    // order: [[ 1, 'desc' ]],
    pageLength: 25,
  };
  let table = $('.datatable-ProductionPayment').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection