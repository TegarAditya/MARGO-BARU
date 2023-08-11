@extends('layouts.admin')
@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1 class="m-0 bold">Billing</h1>
    </div>
</div>
@can('bill_create')
<div style="margin-bottom: 10px;" class="row">
    <div class="col-lg-12">
        <form action="{{ route('admin.bills.generate') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-danger"><i class="fas fa-sync fa-spin fa-lg"></i> Generate Saldo</button>
        </form>
    </div>
</div>
@endcan

<div class="card">
    <div class="card-header">
        <strong>REKAP BILLING {{ App\Models\Semester::find(setting('current_semester'))->name }}</strong>
    </div>
    <div class="card-body">
        <form action="{{ route("admin.bills.jangka") }}" enctype="multipart/form-data" method="POST">
            @csrf
            <div class="row mb-5">
                <div class="col row">
                    <div class="col-6">
                        <x-admin.form-group
                            type="text"
                            id="date"
                            name="date"
                            containerClass=" m-0"
                            boxClass=" px-2 py-1"
                            class="form-control-sm"
                            value="{{ request('date', old('date'))}}"
                            placeholder="Pilih Tanggal"
                        >
                            <x-slot name="label">
                                <label class="small mb-0" for="date">Tanggal</label>
                            </x-slot>

                            <x-slot name="right">
                                <button type="button" class="btn btn-sm border-0 btn-default px-2 date-clear" data-action="+" style="display:{{ !request('date', old('date')) ? 'none' : 'block' }}">
                                    <i class="fa fa-times"></i>
                                </button>
                            </x-slot>
                        </x-admin.form-group>
                    </div>
                </div>

                <div class="col-auto align-self-end">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Bill">
            <thead>
                <tr>
                    <th width="10">

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
<script src="https://cdn.jsdelivr.net/npm/@easepick/bundle@1.2.0/dist/index.umd.min.js"></script>
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
        { data: 'salesperson_name', name: 'salesperson.name' },
        { data: 'saldo_awal', name: 'saldo_awal', class: 'text-right' },
        { data: 'jual', name: 'jual', class: 'text-right' },
        { data: 'diskon', name: 'diskon', class: 'text-right' },
        { data: 'retur', name: 'retur', class: 'text-right' },
        { data: 'bayar', name: 'bayar', class: 'text-right' },
        { data: 'potongan', name: 'potongan', class: 'text-right' },
        { data: 'saldo_akhir', name: 'saldo_akhir', class: 'text-right' },
        { data: 'actions', name: '{{ trans('global.actions') }}', class: 'text-center' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 50,
  };
  let table = $('.datatable-Bill').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });

  var picker = new easepick.create({
        element: $('#date').get(0),
        css: [
            'https://cdn.jsdelivr.net/npm/@easepick/bundle@1.2.0/dist/index.css',
        ],
        plugins: ['RangePlugin', 'LockPlugin'],
        RangePlugin: {
            tooltip: true,
        },
        LockPlugin: {
            maxDate: new Date(),
        },
    });

    picker.on('select', function(e) {
        $('#date').trigger('change');
        $('.date-clear').show();
    });

    $('.date-clear').on('click', function(e) {
        e.preventDefault();

        picker.clear();
        $(e.currentTarget).hide();
    });
});

</script>
@endsection
