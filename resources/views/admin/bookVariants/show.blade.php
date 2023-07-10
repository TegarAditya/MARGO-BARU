@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.bookVariant.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            Tipe Buku
                        </th>
                        <td>
                            {{ App\Models\BookVariant::TYPE_SELECT[$bookVariant->type] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookVariant.fields.code') }}
                        </th>
                        <td>
                            {{ $bookVariant->code }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Name
                        </th>
                        <td>
                            {{ $bookVariant->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookVariant.fields.jenjang') }}
                        </th>
                        <td>
                            {{ $bookVariant->jenjang->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookVariant.fields.kurikulum') }}
                        </th>
                        <td>
                            {{ $bookVariant->kurikulum->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookVariant.fields.semester') }}
                        </th>
                        <td>
                            {{ $bookVariant->semester->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookVariant.fields.halaman') }}
                        </th>
                        <td>
                            {{ $bookVariant->halaman->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookVariant.fields.price') }}
                        </th>
                        <td>
                            {{ money($bookVariant->price) }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            HPP
                        </th>
                        <td>
                            {{ money($bookVariant->cost) }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookVariant.fields.stock') }}
                        </th>
                        <td>
                            <b>{{ angka($bookVariant->stock) }} {{ $bookVariant->unit->name ?? '' }}<b>
                        </td>
                    </tr>
                </tbody>
            </table>
            <h3 class="mt-5 mb-3">History Product Movement</h3>
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-StockMovement">
                    <thead>
                        <tr>
                            <th></th>
                            <th>
                                {{ trans('cruds.stockMovement.fields.type') }}
                            </th>
                            <th>
                                {{ trans('cruds.stockMovement.fields.reference') }}
                            </th>
                            <th>
                                {{ trans('cruds.stockMovement.fields.quantity') }}
                            </th>
                            <th>
                                Stock
                            </th>
                            <th>
                                {{ trans('cruds.stockMovement.fields.created_at') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $stock_actual = $product->stock;
                        @endphp
                        @foreach($stockMovements as $key => $stockMovement)
                            <tr data-entry-id="{{ $stockMovement->id }}">
                                <td></td>
                                <td>
                                    {{ App\Models\StockMovement::TYPE_SELECT[$stockMovement->type] ?? '' }}
                                </td>
                                <td>
                                    @if ($stockMovement->type == 'order')
                                        {{ $stockMovement->referensi->no_order }}
                                    @elseif ($stockMovement->type == 'faktur')
                                        {{ $stockMovement->referensi->no_invoice }}
                                    @elseif ($stockMovement->type == 'adjustment')
                                        {{ $stockMovement->referensi->date.'('.App\Models\StockAdjustment::OPERATION_SELECT[$stockMovement->referensi->operation] .')' }}
                                    @endif
                                </td>
                                <td>
                                    {{ $stockMovement->quantity ?? '' }}
                                </td>
                                <td>
                                    {{ $stock_actual }}
                                </td>
                                <td>
                                    {{ $stockMovement->created_at ?? '' }}
                                </td>
                            </tr>
                        @php
                            $stock_actual = $stock_actual - $stockMovement->quantity;
                        @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
@parent
<script>
$(function () {
    let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
    $.extend(true, $.fn.dataTable.defaults, {
        orderCellsTop: true,
        order: [[ 5, 'desc' ]],
        pageLength: 50,
    });
    let table = $('.datatable-StockMovement:not(.ajaxTable)').DataTable({ buttons: dtButtons })
    $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });
})
</script>
@endsection
