@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.deliveryOrder.title') }}
    </div>

    <div class="card-body">
        <div class="row">

            <div class="col">
                <a class="btn btn-default" href="{{ url()->previous() }}">
                    Back
                </a>
            </div>

            <div class="col-auto">

            </div>
        </div>

        <div class="model-detail mt-3">

            <section class="py-3" id="modelDetail">
                <h6>Detail Sales Order</h6>

                <table class="table table-sm border m-0">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.deliveryOrder.fields.no_suratjalan') }}
                            </th>
                            <td>
                                {{ $deliveryOrder->no_suratjalan }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.deliveryOrder.fields.date') }}
                            </th>
                            <td>
                                {{ $deliveryOrder->date }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.deliveryOrder.fields.semester') }}
                            </th>
                            <td>
                                {{ $deliveryOrder->semester->name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.deliveryOrder.fields.salesperson') }}
                            </th>
                            <td>
                                {{ $deliveryOrder->salesperson->name ?? '' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <section class="border-top py-3">
                <div class="row mb-2">
                    <div class="col">
                        <h6>Daftar Produk</h6>

                        <p class="mb-0">Total Produk: {{ $orders->count() }}</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body px-3 py-2">
                        <table class="table table-sm table-bordered m-0">
                            <thead>
                                <tr>
                                    <th class="text-center" width="1%">No.</th>
                                    <th>Nama Produk</th>
                                    <th class="text-center px-2" width="1%">Halaman</th>
                                    <th class="text-center px-2" width="1%">Estimasi</th>
                                    <th class="text-center px-2" width="1%">Dikirim</th>
                                    <th class="text-center px-2" width="1%">Retur</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($orders as $order)
                                    @php
                                    $product = $order->product;
                                    @endphp
                                    <tr>
                                        <td class="text-right px-3">{{ $loop->iteration }}.</td>
                                        <td>{{ $product->name }}</td>
                                        <td class="text-center px-2">{{ $product->halaman->code }}</td>
                                        <td class="text-center px-2">{{ $order->quantity }}</td>
                                        <td class="text-center px-2">{{ $order->moved }}</td>
                                        <td class="text-center px-2">{{ $order->retur }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>



@endsection
