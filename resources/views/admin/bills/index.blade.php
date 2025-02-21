@extends('layouts.admin')
@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1 class="m-0 bold">Billing</h1>
    </div>
</div>

<div style="margin-bottom: 10px;" class="row">
    @can('bill_create')
        <div class="col-2">
            <form action="{{ route('admin.bills.generate') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger btn-block"><i class="fas fa-sync fa-spin fa-lg"></i> Generate Saldo</button>
            </form>
        </div>
    @endcan
    @can('direktur')
        <div class="col-4">
            <a href="{{ route('admin.bills.reportDirektur') }}" class="btn btn-success btn-block"><i class="fas fa-file-export"></i> Export Rekap Billing Direktur</a>
        </div>
    @endcan
    <div class="col-2">
        <a href="{{ route('admin.bills.eksportRekapBilling') }}" class="btn btn-warning btn-block"><i class="fas fa-file-export"></i> Export Rekap Billing</a>
    </div>

    <div class="col-2">
        <a href="{{ route('admin.bills.salespersonIndex') }}" class="btn btn-secondary btn-block"><i class="fas fa-sync"></i> Check Rebalancing Sales</a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <strong>REKAP BILLING {{ App\Models\Semester::find(setting('current_semester'))->name }}</strong>
    </div>
    <div class="card-body">
        <form action="{{ route("admin.bills.jangka") }}" enctype="multipart/form-data" method="POST">
            @csrf
            <div class="row mb-5">
                <div class="col row">
                    <div class="col-8">
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
                    <button type="submit" class="btn btn-primary mr-2">Filter</button>
                    <button type="submit" value="export" name="export" class="btn btn-warning">Export</button>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label for="semester_id">{{ trans('cruds.cetak.fields.semester') }}</label>
                        <select class="form-control select2 {{ $errors->has('semester') ? 'is-invalid' : '' }}" name="semester_id" id="semester_id">
                            @foreach($semesters as $id => $entry)
                                <option value="{{ $id }}" {{ (old('semester_id') ? old('semester_id') : setting('current_semester') ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('semester'))
                            <span class="text-danger">{{ $errors->first('semester') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.cetak.fields.semester_helper') }}</span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <button id="buttonChange" class="btn btn-primary mr-2">Pindah Semester</button>
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
                        Adjustment
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
    ajax: {
        url: "{{ route('admin.bills.index') }}",
        data: function(data) {
            data.semester = $('#semester_id').val()
        }
    },
    columns: [
        { data: 'placeholder', name: 'placeholder' },
        { data: 'salesperson_name', name: 'salesperson.name', visible: false },
        { data: 'sales', name: 'sales' },
        { data: 'saldo_awal', name: 'saldo_awal', class: 'text-right', render: function(value) { return numeral(value).format('$0,0'); } },
        { data: 'jual', name: 'jual', class: 'text-right', render: function(value) { return numeral(value).format('$0,0'); } },
        { data: 'diskon', name: 'diskon', class: 'text-right', render: function(value) { return numeral(value).format('$0,0'); } },
        { data: 'adjustment', name: 'adjustment', class: 'text-right', render: function(value) { return numeral(value).format('$0,0'); } },
        { data: 'retur', name: 'retur', class: 'text-right', render: function(value) { return numeral(value).format('$0,0'); } },
        { data: 'bayar', name: 'bayar', class: 'text-right', render: function(value) { return numeral(value).format('$0,0'); } },
        { data: 'potongan', name: 'potongan', class: 'text-right', render: function(value) { return numeral(value).format('$0,0'); } },
        { data: 'saldo_akhir', name: 'saldo_akhir', class: 'text-right', render: function(value) { return numeral(value).format('$0,0'); } },
        { data: 'actions', name: '{{ trans('global.actions') }}', class: 'text-center' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'asc' ]],
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

    $("#buttonChange").click(function(event) {
        event.preventDefault();
        table.ajax.reload();
    });
});

</script>
@endsection
