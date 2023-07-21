@extends('layouts.admin')
@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1 class="m-0 bold">Estimasi Sales</h1>
    </div>
</div>
<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.salesOrder.title') }}
    </div>

    <div class="card-body">
        <div class="model-detail">
            <section class="py-3" id="modelDetail">
                <table class="table table-sm border m-0">
                    <tbody>
                        <tr>
                            <th width="150">
                                Semester
                            </th>
                            <td>
                                {{ $salesOrder->semester->name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Sales
                            </th>
                            <td>
                                {{ $salesOrder->salesperson->name ?? '' }}
                            </td>
                        </tr>
                        {{-- <tr>
                            <th>
                                Payment Type
                            </th>
                            <td>
                                {{ App\Models\SalesOrder::PAYMENT_TYPE_SELECT[$salesOrder->payment_type] }}
                            </td>
                        </tr> --}}
                    </tbody>
                </table>
            </section>
            @php
                $cash = $orders->where('payment_type', 'cash')->sortBy('product.jenjang_id')->sortBy('product.kelas_id')->sortBy('product.mapel_id');
                $retur = $orders->where('payment_type', 'retur')->sortBy('product.jenjang_id')->sortBy('product.kelas_id')->sortBy('product.mapel_id');
            @endphp
            <section class="border-top py-3">
                <div class="row mb-2">
                    <div class="col">
                        <h6>Daftar Produk Estimasi Pembayaran Cash</h6>

                        <p class="mb-0">Total Produk: {{ $cash->count() }}</p>
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
                                @php
                                    $totalestimasi = 0;
                                    $totaldikirim = 0;
                                    $totalretur = 0;
                                @endphp
                                @foreach ($cash as $order)
                                    @php
                                    $product = $order->product;
                                    $totalestimasi += $order->quantity;
                                    $totaldikirim += $order->moved;
                                    $totalretur += $order->retur;
                                    @endphp
                                    <tr>
                                        <td class="text-right px-3">{{ $loop->iteration }}.</td>
                                        <td>{{ $product->name }}</td>
                                        <td class="text-center px-2">{{ $product->halaman->code }}</td>
                                        <td class="text-center px-2">{{ angka($order->quantity) }}</td>
                                        <td class="text-center px-2">{{ angka($order->moved) }}</td>
                                        <td class="text-center px-2">{{ angka($order->retur) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-center px-3" colspan="3"><strong>Total</strong></td>
                                    <td class="text-center px-2"><strong>{{ angka($totalestimasi) }}</strong></td>
                                    <td class="text-center px-2"><strong>{{ angka($totaldikirim) }}</strong></td>
                                    <td class="text-center px-2"><strong>{{ angka($totalretur) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </section>
            <section class="border-top py-3">
                <div class="row mb-2">
                    <div class="col">
                        <h6>Daftar Produk Estimasi Pembayaran Retur</h6>

                        <p class="mb-0">Total Produk: {{ $retur->count() }}</p>
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
                                @php
                                    $totalestimasi = 0;
                                    $totaldikirim = 0;
                                    $totalretur = 0;
                                @endphp
                                @foreach ($retur as $order)
                                    @php
                                    $product = $order->product;
                                    $totalestimasi += $order->quantity;
                                    $totaldikirim += $order->moved;
                                    $totalretur += $order->retur;
                                    @endphp
                                    <tr>
                                        <td class="text-right px-3">{{ $loop->iteration }}.</td>
                                        <td>{{ $product->name }}</td>
                                        <td class="text-center px-2">{{ $product->halaman->code }}</td>
                                        <td class="text-center px-2">{{ angka($order->quantity) }}</td>
                                        <td class="text-center px-2">{{ angka($order->moved) }}</td>
                                        <td class="text-center px-2">{{ angka($order->retur) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-center px-3" colspan="3"><strong>Total</strong></td>
                                    <td class="text-center px-2"><strong>{{ angka($totalestimasi) }}</strong></td>
                                    <td class="text-center px-2"><strong>{{ angka($totaldikirim) }}</strong></td>
                                    <td class="text-center px-2"><strong>{{ angka($totalretur) }}</strong></td>
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
