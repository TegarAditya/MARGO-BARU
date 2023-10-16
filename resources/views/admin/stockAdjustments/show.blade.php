@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.stockAdjustment.title') }}
    </div>

    <div class="card-body">
        <div class="model-detail">
            <h6>Stock Adjustment</h6>
            <section class="py-3" id="modelDetail">
                <table class="table table-sm border m-0">
                    <tbody>
                        <tr>
                            <th  width="150">
                                {{ trans('cruds.stockAdjustment.fields.date') }}
                            </th>
                            <td>
                                {{ $stockAdjustment->date }}
                            </td>
                        </tr>
                        <tr>
                            <th  width="150">
                                {{ trans('cruds.stockAdjustment.fields.type') }}
                            </th>
                            <td>
                                {{ App\Models\StockAdjustment::TYPE_SELECT[$stockAdjustment->type] ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.stockAdjustment.fields.operation') }}
                            </th>
                            <td>
                                {{ App\Models\StockAdjustment::OPERATION_SELECT[$stockAdjustment->operation] ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.stockAdjustment.fields.reason') }}
                            </th>
                            <td>
                                {{ $stockAdjustment->reason }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.stockAdjustment.fields.note') }}
                            </th>
                            <td>
                                {{ $stockAdjustment->note }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Dibuat Oleh
                            </th>
                            <td>
                                {{ $stockAdjustment->created_by->name }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Diedit Oleh
                            </th>
                            <td>
                                {{ $stockAdjustment->updated_by->name }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <section class="border-top py-3">
                <div class="row mb-2">
                    <div class="col">
                        <h6>Daftar Produk</h6>

                        <p class="mb-0">Total Produk: <strong>{{ $adjustment_details->count() }}</strong></p>
                    </div>
                </div>

                @if($stockAdjustment->type == 'book')
                    <div class="card">
                        <div class="card-body px-3 py-2">
                            <table class="table table-sm table-bordered m-0">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="1%">No.</th>
                                        <th>Nama Produk</th>
                                        <th class="text-center px-2" width="1%">Halaman</th>
                                        <th class="text-center px-2" width="1%">Quantity</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @php
                                        $totalquantity = 0;
                                    @endphp
                                    @foreach ($adjustment_details as $item)
                                        @php
                                        $product = $item->product;
                                        $totalquantity += $item->quantity;
                                        @endphp
                                        <tr>
                                            <td class="text-right px-3">{{ $loop->iteration }}.</td>
                                            <td>{{ $product->name }}</td>
                                            <td class="text-center px-2">{{ $product->halaman->code }}</td>
                                            <td class="text-center px-2">{{ angka($item->quantity) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="text-center px-3" colspan="3"><strong>Total</strong></td>
                                        <td class="text-center px-2"><strong>{{ angka($totalquantity) }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @elseif ($stockAdjustment->type == 'material')
                    <div class="card">
                        <div class="card-body px-3 py-2">
                            <table class="table table-sm table-bordered m-0">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="1%">No.</th>
                                        <th>Nama Material</th>
                                        <th class="text-center px-2" width="1%">Kategori</th>
                                        <th class="text-center px-2" width="1%">Quantity</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @php
                                        $totalquantity = 0;
                                    @endphp
                                    @foreach ($adjustment_details as $item)
                                        @php
                                        $material = $item->material;
                                        $totalquantity += $item->quantity;
                                        @endphp
                                        <tr>
                                            <td class="text-right px-3">{{ $loop->iteration }}.</td>
                                            <td>{{ $material->name }}</td>
                                            <td class="text-center px-2">{{ App\Models\Material::CATEGORY_SELECT[$material->category] }}</td>
                                            <td class="text-center px-2">{{ angka($item->quantity) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="text-center px-3" colspan="3"><strong>Total</strong></td>
                                        <td class="text-center px-2"><strong>{{ angka($totalquantity) }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @endif
            </section>
        </div>
        <div class="row mt-3">

            <div class="col">
                <a class="btn btn-primary" href="{{ url()->previous() }}">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
            </div>

            <div class="col-auto">

            </div>
        </div>
    </div>
</div>



@endsection
