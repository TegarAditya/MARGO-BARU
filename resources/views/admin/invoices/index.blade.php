@extends('layouts.admin')
@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1 class="m-0">Invoice / Faktur</h1>
    </div>
</div>
@can('invoice_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.invoices.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.invoice.title_singular') }}
            </a>
        </div>
    </div>
@endcan
@if ($delivery_orders->count() > 0)
<div class="card">
    <div class="card-header">Daftar Surat Jalan Yang Belum Digenerate</div>
    <div class="card-body">
        <div class="row">
            @foreach ($delivery_orders as $item)
                <div class="col-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><a href="{{ route('admin.invoices.generate', $item->id) }}"><i class="fas fa-file-invoice"></i></a></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ $item->no_suratjalan }}</span>
                            <span class="info-box-number">Surat Jalan</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif
<div class="card">
    <div class="card-header">
        {{ trans('cruds.invoice.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <form id="filterform">
            <div class="row mb-5">
                <div class="col row">
                    <div class="col-6">
                        <div class="form-group mb-0">
                            <label class="small mb-0" for="semester_id">{{ trans('cruds.salesOrder.fields.semester') }}</label>
                            <select class="form-control select2 {{ $errors->has('semester') ? 'is-invalid' : '' }}" name="semester_id" id="semester_id">
                                @foreach($semesters as $id => $entry)
                                    <option value="{{ $id }}" {{ old('semester_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group mb-0">
                            <label class="small mb-0" for="salesperson_id">{{ trans('cruds.salesOrder.fields.salesperson') }}</label>
                            <select class="form-control select2 {{ $errors->has('salesperson') ? 'is-invalid' : '' }}" name="salesperson_id" id="salesperson_id">
                                @foreach($salespeople as $id => $entry)
                                    <option value="{{ $id }}" {{ old('salesperson_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-auto align-self-end">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Invoice">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.invoice.fields.no_faktur') }}
                    </th>
                    <th>
                        {{ trans('cruds.invoice.fields.date') }}
                    </th>
                    <th>
                        {{ trans('cruds.invoice.fields.semester') }}
                    </th>
                    <th>
                        {{ trans('cruds.invoice.fields.salesperson') }}
                    </th>
                    <th>
                        Total
                    </th>
                    <th>
                        Discount
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
    ajax: {
        url: "{{ route('admin.invoices.index') }}",
        data: function(data) {
            data.salesperson = $('#salesperson_id').val(),
            data.semester = $('#semester_id').val()
        }
    },
    columns: [
        { data: 'placeholder', name: 'placeholder' },
        { data: 'no_faktur', name: 'no_faktur', class: 'text-center' },
        { data: 'date', name: 'date', class: 'text-center' },
        { data: 'semester_name', name: 'semester.name', class: 'text-center' },
        { data: 'salesperson_name', name: 'salesperson.name', class: 'text-center' },
        { data: 'total', name: 'total', class: 'text-right' },
        { data: 'discount', name: 'discount', class: 'text-right' },
        { data: 'actions', name: '{{ trans('global.actions') }}', class: 'text-center' }
    ],
    orderCellsTop: true,
    // order: [[ 1, 'desc' ]],
    pageLength: 50,
  };
  let table = $('.datatable-Invoice').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });

    $("#filterform").submit(function(event) {
        event.preventDefault();
        table.ajax.reload();
    });

});

</script>
@endsection
