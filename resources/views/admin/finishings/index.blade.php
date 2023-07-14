@extends('layouts.admin')
@section('content')
@can('finishing_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-primary" href="{{ route('admin.finishings.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.finishing.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.finishing.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <form id="filterform">
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label for="semester_id">{{ trans('cruds.cetak.fields.semester') }}</label>
                        <select class="form-control select2 {{ $errors->has('semester') ? 'is-invalid' : '' }}" name="semester_id" id="semester_id">
                            @foreach($semesters as $id => $entry)
                                <option value="{{ $id }}" {{ old('semester_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('semester'))
                            <span class="text-danger">{{ $errors->first('semester') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.cetak.fields.semester_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="vendor_id">{{ trans('cruds.cetak.fields.vendor') }}</label>
                        <select class="form-control select2 {{ $errors->has('vendor') ? 'is-invalid' : '' }}" name="vendor_id" id="vendor_id">
                            @foreach($vendors as $id => $entry)
                                <option value="{{ $id }}" {{ old('vendor_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('vendor'))
                            <span class="text-danger">{{ $errors->first('vendor') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.cetak.fields.vendor_helper') }}</span>
                    </div>
                </div>
            </div>

            <div class="form-group mt-3">
                <button class="btn btn-success" type="submit">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Finishing">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.finishing.fields.no_spk') }}
                    </th>
                    <th>
                        {{ trans('cruds.finishing.fields.date') }}
                    </th>
                    <th>
                        {{ trans('cruds.finishing.fields.semester') }}
                    </th>
                    <th>
                        {{ trans('cruds.finishing.fields.vendor') }}
                    </th>
                    <th>
                        Ongkos
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
        url: "{{ route('admin.finishings.index') }}",
        data: function(data) {
            data.vendor = $('#vendor_id').val()
            data.semester = $('#semester_id').val()
        }
    },
    columns: [
        { data: 'placeholder', name: 'placeholder' },
        { data: 'no_spk', name: 'no_spk', class: 'text-center' },
        { data: 'date', name: 'date', class: 'text-center' },
        { data: 'semester_name', name: 'semester.name', class: 'text-center' },
        { data: 'vendor_name', name: 'vendor.name', class: 'text-center' },
        { data: 'total_cost', name: 'total_cost', class: 'text-right' },
        { data: 'actions', name: '{{ trans('global.actions') }}', class: 'text-center' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 50,
  };
    let table = $('.datatable-Finishing').DataTable(dtOverrideGlobals);
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