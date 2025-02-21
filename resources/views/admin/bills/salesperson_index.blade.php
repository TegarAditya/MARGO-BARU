@extends('layouts.admin')
@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1 class="m-0 bold">Billing</h1>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <strong>REKAP BILLING</strong>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <label class="required" for="salesperson_id">{{ trans('cruds.bill.fields.salesperson') }}</label>
                    <select class="form-control select2 {{ $errors->has('salesperson') ? 'is-invalid' : '' }}" name="salesperson_id" id="salesperson_id">
                        @foreach($salespeople as $id => $entry)
                            <option value="{{ $id }}" {{ old('salesperson_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('salesperson'))
                        <span class="text-danger">{{ $errors->first('salesperson') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.bill.fields.salesperson_helper') }}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-4">
                <button id="changeButton" class="btn btn-primary mr-2">Ganti Salesperson</button>
            </div>
        </div>
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
                        {{ trans('cruds.bill.fields.semester') }}
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
                        Piutang Semester
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
        url: "{{ route('admin.bills.salespersonIndex') }}",
        data: function(data) {
            data.salesperson = $('#salesperson_id').val()
        }
    },
    columns: [
        { data: 'placeholder', name: 'placeholder' },
        { data: 'salesperson_name', name: 'salesperson.name', visible: false },
        { data: 'sales', name: 'sales' },
        { data: 'semester_name', name: 'semester_name' },
        { data: 'saldo_awal', name: 'saldo_awal', class: 'text-right', render: function(value) { return numeral(value).format('$0,0'); } },
        { data: 'jual', name: 'jual', class: 'text-right', render: function(value) { return numeral(value).format('$0,0'); } },
        { data: 'diskon', name: 'diskon', class: 'text-right', render: function(value) { return numeral(value).format('$0,0'); } },
        { data: 'adjustment', name: 'adjustment', class: 'text-right', render: function(value) { return numeral(value).format('$0,0'); } },
        { data: 'retur', name: 'retur', class: 'text-right', render: function(value) { return numeral(value).format('$0,0'); } },
        { data: 'bayar', name: 'bayar', class: 'text-right', render: function(value) { return numeral(value).format('$0,0'); } },
        { data: 'potongan', name: 'potongan', class: 'text-right', render: function(value) { return numeral(value).format('$0,0'); } },
        { data: 'saldo_akhir', name: 'saldo_akhir', class: 'text-right', render: function(value) { return numeral(value).format('$0,0'); } },
        { data: 'piutang', name: 'piutang', class: 'text-right', render: function(value) { return numeral(value).format('$0,0'); } },
        { data: 'actions', name: '{{ trans('global.actions') }}', class: 'text-center' }
    ],
    orderCellsTop: true,
    // order: [[ 1, 'asc' ]],
    pageLength: 50,
    };
    let table = $('.datatable-Bill').DataTable(dtOverrideGlobals);
    $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });

    document.getElementById('changeButton').addEventListener('click', (event) => {
        console.log('aaa');
        event.preventDefault();
        table.ajax.reload();
    });
});
</script>
@endsection
