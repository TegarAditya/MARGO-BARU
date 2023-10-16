@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
        {{ trans('cruds.stockMovement.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-StockMovement">
            <thead>
                <tr>
                    <th>
                        ID
                    </th>
                    <th>
                        Reference
                    </th>
                    <th>
                        Move
                    </th>
                    <th width="150">
                        Code
                    </th>
                    <th>
                        Name
                    </th>
                    <th>
                        {{ trans('cruds.stockMovement.fields.quantity') }}
                    </th>
                    <th>
                        Diedit Oleh
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
        ajax: "{{ route('admin.stock-movements.index') }}",
        columns: [
            { data: 'id', name: 'id', class: 'text-center' },
            { data: 'reference', name: 'reference', class: 'text-center' },
            { data: 'movement_type', name: 'movement_type', class: 'text-center' },
            { data: 'product_code', name: 'product.code', class: 'text-center' },
            { data: 'product_name', name: 'product.name' },
            { data: 'quantity', name: 'quantity', class: 'text-center' },
            { data: 'pengedit', name: 'pengedit', class: 'text-center' }
        ],
        orderCellsTop: true,
        // order: [[ 1, 'desc' ]],
        pageLength: 50,
    };
    let table = $('.datatable-StockMovement').DataTable(dtOverrideGlobals);
    $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });
});

</script>
@endsection
