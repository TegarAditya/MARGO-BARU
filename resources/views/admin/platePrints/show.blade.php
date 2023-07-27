@extends('layouts.admin')
@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1 class="m-0 bold">Produksi Cetak Plate</h1>
    </div>
</div>
<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.platePrint.title') }}
    </div>

    <div class="card-body">
        <div class="model-detail">
            <h6>Order Plate Cetak</h6>
            <section class="py-3" id="modelDetail">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.platePrint.fields.no_spk') }}
                            </th>
                            <td>
                                {{ $platePrint->no_spk }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.platePrint.fields.date') }}
                            </th>
                            <td>
                                {{ $platePrint->date }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.platePrint.fields.semester') }}
                            </th>
                            <td>
                                {{ $platePrint->semester->name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.platePrint.fields.vendor') }}
                            </th>
                            <td>
                                {{ $platePrint->vendor->code ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.platePrint.fields.note') }}
                            </th>
                            <td>
                                {{ $platePrint->note }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <section class="border-top py-3">
                <div class="row mb-2">
                    <div class="col">
                        <h6>Daftar Plate</h6>

                        <p class="mb-0">Total Plate: <strong>{{ $items->count() }}</strong></p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body px-3 py-2">
                        <table class="table table-sm table-bordered m-0">
                            <thead>
                                <tr>
                                    <th class="text-center" width="1%">No.</th>
                                    <th width="40%">Nama Produk</th>
                                    <th class="text-center px-2" width="20%">Plate</th>
                                    <th class="text-center px-2" width="10%">Plate Quantity</th>
                                    <th class="text-center px-2" width="20%">Chemical</th>
                                    <th class="text-center px-2" width="10%">Chemical Quantity</th>
                                </tr>
                            </thead>

                            <tbody>
                                @php
                                    $totalplate = 0;
                                    $totalchemical = 0;
                                @endphp
                                @foreach ($items as $item)
                                    @php
                                        $totalplate += $item->plate_qty;
                                        $totalchemical += $item->chemical_qty;
                                    @endphp
                                    <tr>
                                        <td class="text-right px-3">{{ $loop->iteration }}.</td>
                                        <td>{{ $item->product->name }}</td>
                                        <td class="text-center px-2">{{ $item->plate->name }}</td>
                                        <td class="text-center px-2">{{ angka($item->plate_qty) }}</td>
                                        <td class="text-center px-2">{{ $item->chemical->name }}</td>
                                        <td class="text-center px-2">{{ $item->chemical_qty }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-center px-3" colspan="3"><strong>Total</strong></td>
                                    <td class="text-center px-2"><strong>{{ angka($totalplate) }}</strong></td>
                                    <td></td>
                                    <td class="text-center px-2"><strong>{{ $totalchemical }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
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
