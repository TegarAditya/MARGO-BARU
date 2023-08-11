@extends('layouts.admin')
@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1 class="m-0 bold">Surat Jalan Plate</h1>
    </div>
</div>
<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.deliveryPlate.title') }}
    </div>

    <div class="card-body">
        <div class="model-detail">
            <h6>Surat Jalan Plate</h6>
            <section class="py-3" id="modelDetail">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.deliveryPlate.fields.no_suratjalan') }}
                            </th>
                            <td>
                                {{ $deliveryPlate->no_suratjalan }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.deliveryPlate.fields.date') }}
                            </th>
                            <td>
                                {{ $deliveryPlate->date }}
                            </td>
                        </tr>
                        @if($deliveryPlate->vendor)
                            <tr>
                                <th>
                                    {{ trans('cruds.deliveryPlate.fields.vendor') }}
                                </th>
                                <td>
                                    {{ $deliveryPlate->vendor->name ?? '' }}
                                </td>
                            </tr>
                        @endif
                        @if($deliveryPlate->customer)
                            <tr>
                                <th>
                                    {{ trans('cruds.deliveryPlate.fields.customer') }}
                                </th>
                                <td>
                                    {{ $deliveryPlate->customer }}
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <th>
                                {{ trans('cruds.deliveryPlate.fields.note') }}
                            </th>
                            <td>
                                {{ $deliveryPlate->note }}
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
                                    <th>Nama Mapel</th>
                                    <th class="text-center px-2" width="20%">Plate</th>
                                    <th class="text-center px-2" width="10%">Jumlah Kirim</th>
                                </tr>
                            </thead>

                            <tbody>
                                @php
                                    $totalplate = 0;
                                @endphp
                                @foreach ($items as $item)
                                    @php
                                        $totalplate += $item->estimasi;
                                    @endphp
                                    <tr>
                                        <td class="text-right px-3">{{ $loop->iteration }}.</td>
                                        <td class="text-center px-3">{{ $item->product ? $item->product->name : $item->product_text }}</td>
                                        <td class="text-center px-2">{{ $item->plate ? $item->plate->name : 'Belum Tahu' }}</td>
                                        <td class="text-center px-2">{{ angka($item->estimasi) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-center px-3" colspan="3"><strong>Total</strong></td>
                                    <td class="text-center px-2"><strong>{{ angka($totalplate) }}</strong></td>
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
