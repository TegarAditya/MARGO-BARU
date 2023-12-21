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
                            <b>{{ $bookVariant->code }}</b>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Name
                        </th>
                        <td>
                            {{ $bookVariant->short_name }}
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
                            Isi
                        </th>
                        <td>
                            {{ $bookVariant->isi?->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Cover
                        </th>
                        <td>
                            {{ $bookVariant->cover?->name ?? '' }}
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
                    <tr>
                        <th>
                            Components
                        </th>
                        <td>
                            @foreach($bookVariant->components as $key => $components)
                                <a class="px-1" href="{{route('admin.book-variants.show', $components->id)}}" title="Show">
                                    <i class="fas fa-eye text-success fa-lg"></i>
                                </a>
                                <span class="label label-info">{{ $components->name }} - <b>{{ $components->stock }} {{ $components->unit->name }}</b></span>
                                <br>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Diedit Oleh
                        </th>
                        <td>
                            <b>{{ $bookVariant->pengedit->name ?? '' }}<b>
                        </td>
                    </tr>
                </tbody>
            </table>
            <h3 class="mt-5 mb-3">History Product Movement</h3>
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-movement">
                    <thead>
                        <tr>
                            <th>

                            </th>
                            <th>
                                Movement
                            </th>
                            <th>
                                Reference
                            </th>
                            <th>
                                Stock Awal
                            </th>
                            <th>
                                Quantity
                            </th>
                            <th>
                                Stock Akhir
                            </th>
                            <th>
                                Date
                            </th>
                            <th>
                                Diedit Oleh
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $stock_actual = $bookVariant->stock;
                        @endphp
                        @foreach($stockMovements as $key => $stockMovement)
                            <tr data-entry-id="{{ $stockMovement->id }}">
                                <td></td>
                                <td class="text-center">
                                    {{ App\Models\StockMovement::MOVEMENT_TYPE_SELECT[$stockMovement->movement_type] ?? '' }}
                                    @if($stockMovement->reversal_of_id)
                                        <br>
                                        (Reversal)
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($stockMovement->transaction_type == 'adjustment')
                                        <span class="mr-2"><a href="{{ route('admin.stock-adjustments.show', $stockMovement->reference->id) }}"><i class="fas fa-eye text-success fa-lg"></i></a></span> {{ 'Adjustment Tanggal :'. $stockMovement->reference->date }}
                                    @elseif ($stockMovement->transaction_type == 'delivery')
                                        <span class="mr-2"><a href="{{ route('admin.delivery-orders.show', $stockMovement->reference->id) }}"><i class="fas fa-eye text-success fa-lg"></i></a></span> {{ $stockMovement->reference->no_suratjalan}}
                                    @elseif ($stockMovement->transaction_type == 'retur')
                                        <span class="mr-2"><a href="{{ route('admin.return-goods.show', $stockMovement->reference->id) }}"><i class="fas fa-eye text-success fa-lg"></i></a></span> {{ $stockMovement->reference->no_retur }}
                                    @elseif ($stockMovement->transaction_type == 'cetak')
                                        <span class="mr-2"><a href="{{ route('admin.cetaks.show', $stockMovement->reference->id) }}"><i class="fas fa-eye text-success fa-lg"></i></a></span> {{ $stockMovement->reference->no_spc }}
                                    @elseif ($stockMovement->transaction_type == 'produksi')
                                        <span class="mr-2"><a href="{{ route('admin.finishings.show', $stockMovement->reference->id) }}"><i class="fas fa-eye text-success fa-lg"></i></a></span> {{ $stockMovement->reference->no_spk }}
                                        @if ($stockMovement->finishing_masuk)
                                            <br>
                                            <span class="mr-2"><a href="{{ route('admin.finishing-masuks.show', $stockMovement->finishing_masuk) }}"><i class="fas fa-eye text-success fa-lg"></i></a></span> {{ $stockMovement->masuk->no_spk }}
                                        @endif
                                    @elseif ($stockMovement->transaction_type == 'awal')
                                        Stock Awal
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{ angka($stock_actual - $stockMovement->quantity) }}
                                </td>
                                <td class="text-center">
                                    {{ angka($stockMovement->quantity) }}
                                </td>
                                <td class="text-center">
                                    {{ angka($stock_actual) }}
                                </td>
                                <td class="text-center">
                                    {{ $stockMovement->created_at ?? '' }}
                                </td>
                                <td class="text-center">
                                    {{ $stockMovement->pengedit ? $stockMovement->pengedit->name : '' }}
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
       $('.datatable-movement').DataTable({
         'paging'      : true,
         'lengthChange': false,
         'searching'   : false,
         'ordering'    : false,
         'info'        : true,
         'autoWidth'   : false,
         'pageLength'  : 50
       })
     })
</script>
@endsection
