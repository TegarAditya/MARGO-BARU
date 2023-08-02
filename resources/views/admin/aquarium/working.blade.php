@extends('layouts.admin')
@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1 class="m-0 bold">Produksi Cetak Plate</h1>
    </div>
</div>
<div class="card">
    <div class="card-header">
        {{ trans('cruds.platePrint.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-PlatePrint">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.platePrint.fields.no_spk') }}
                    </th>
                    <th>
                        {{ trans('cruds.platePrint.fields.date') }}
                    </th>
                    <th>
                        {{ trans('cruds.platePrint.fields.semester') }}
                    </th>
                    <th>
                        {{ trans('cruds.platePrint.fields.type') }}
                    </th>
                    <th>
                        {{ trans('cruds.platePrint.fields.customer') }}
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
    ajax: "{{ route('admin.aquarium.working') }}",
    columns: [
        { data: 'placeholder', name: 'placeholder' },
        { data: 'no_spk', name: 'no_spk', class: 'text-center' },
        { data: 'date', name: 'date', class: 'text-center'  },
        { data: 'semester_name', name: 'semester.name', class: 'text-center'  },
        { data: 'type', name: 'type', class: 'text-center'  },
        { data: 'customer', name: 'customer', class: 'text-center'  },
        { data: 'actions', name: '{{ trans('global.actions') }}', class: 'text-center'  }
    ],
    orderCellsTop: true,
    order: [[ 4, 'desc' ]],
    pageLength: 50,
  };
  let table = $('.datatable-PlatePrint').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });

});

</script>
@endsection
