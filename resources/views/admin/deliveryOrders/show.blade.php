@extends('layouts.admin')
@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="m-0 bold">Surat Jalan No {{ $deliveryOrder->no_suratjalan }}</h2>
    </div>
</div>
<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.deliveryOrder.title_singular') }}
    </div>

    <div class="card-body">
        <div class="model-detail">
            <section class="py-3">
                <div class="card">
                    <div class="card-body px-3 py-2">
                        <div class="row">
                            <div class="col-6 mb-1">
                                <span class="badge badge-info">Pengiriman</span>
                            </div>

                            <div class="col-6 text-right">
                                <a href="{{ route('admin.delivery-orders.edit', $deliveryOrder->id) }}" class="border-bottom">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                            </div>

                            <div class="col-3">
                                <p class="mb-0 text-sm">
                                    No. Surat Jalan
                                    <br />
                                    <strong>{{ $deliveryOrder->no_suratjalan }}</strong>

                                    <a href="{{ route('admin.delivery-orders.printSj', $deliveryOrder->id) }}" class="fa fa-print ml-1 text-info" title="Print Surat Jalan" target="_blank"></a>
                                </p>
                            </div>

                            <div class="col text-right">
                                <span>Tanggal<br />{{ $deliveryOrder->date }}</span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-4">
                                <p class="mb-0 text-sm">
                                    Salesman
                                    <br />
                                    <strong>{{ $deliveryOrder->salesperson->name }} - {{ $deliveryOrder->salesperson->marketing_area->name }} </strong>
                                </p>
                            </div>

                            <div class="col-4">
                                <p class="mb-0 text-sm">
                                    Semester
                                    <br />
                                    <strong>{{ $deliveryOrder->semester->name }}</strong>
                                </p>
                            </div>
                        </div>

                        <p class="mt-2 mb-1">
                            <strong>Produk</strong>
                        </p>

                        <table class="table table-sm table-bordered m-0">
                            <thead>
                                <tr>
                                    <th class="text-center" width="1%">No.</th>
                                    <th>Jenjang</th>
                                    <th>Tema/Mapel</th>
                                    <th class="text-center px-3" width="1%">Quantity</th>
                                </tr>
                            </thead>

                            <tbody>
                                @php
                                    $total_item = 0;
                                @endphp
                                @forelse ($delivery_items as $item)
                                    @php
                                    $product = $item->product;
                                    $total_item += $item->quantity;
                                    @endphp
                                    <tr>
                                        <td class="text-right px-3">{{ $loop->iteration }}.</td>
                                        <td class="text-center">{{ $product->jenjang->name ?? '' }} - {{ $product->kurikulum->code ?? '' }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td class="text-center px-3">{{ $item->quantity }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="px-3" colspan="6">Tidak ada produk</td>
                                    </tr>
                                @endforelse
                            </tbody>

                            <tfoot>
                                <tr>
                                    <td class="text-center px-3" colspan="3"><strong>Total Quantity</strong></td>
                                    <td class="text-center px-3">
                                        <strong>{{ angka($total_item) }}</strong>
                                    </td>
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
