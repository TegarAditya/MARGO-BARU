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
                                {{ $semester->name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Sales
                            </th>
                            <td>
                                {{ $salesperson->name ?? 'Internal' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>

            @foreach ($grouped as $key => $value)
                <section class="border-top py-3">
                    <div class="row mb-2">
                        <div class="col">
                            <h6>Estimasi Jenjang {{ $key }}</h6>

                            <p class="mb-0">Total Produk: {{ $value->count() }}</p>
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
                                        <th class="text-center px-2">Estimasi Per-Formulir</th>
                                        <th class="text-center px-2" width="1%">Estimasi</th>
                                        <th class="text-center px-2" width="1%">Terkirim</th>
                                        <th class="text-center px-2" width="1%">Sisa</th>
                                        <th class="text-center px-2" width="1%">Retur</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @php
                                        $totalestimasi = 0;
                                        $totaldikirim = 0;
                                        $totalsisa = 0;
                                        $totalretur = 0;
                                    @endphp
                                    @foreach ($value as $order)
                                        @php
                                        $estimation_items = \App\Models\EstimationItem::with('estimation')->where('salesperson_id', $salesperson->id ?? null)->where('semester_id', $semester->id)->where('product_id', $order->id)->get();

                                        $estimasi = $salesOrder->where('product_id', $order->id)->first()->quantity;
                                        $terkirim = $salesOrder->where('product_id', $order->id)->first()->moved;
                                        $retur = $salesOrder->where('product_id', $order->id)->first()->retur;
                                        $sisa = max(0, $estimasi - $terkirim);

                                        $test = $terkirim;

                                        $totalestimasi += $estimasi;
                                        $totaldikirim += $terkirim;
                                        $totalsisa += $sisa;
                                        $totalretur += $retur;
                                        @endphp
                                        <tr>
                                            <td class="text-right px-3">{{ $loop->iteration }}.</td>
                                            <td>{{ $order->name }}</td>
                                            <td class="text-center">{{ $order->halaman->code }}</td>
                                            <td class="text-center">
                                                @foreach ($estimation_items as $item)
                                                    @php
                                                        $check = ($item->quantity > $test) ? true : false;
                                                        $check ? null : $test -= $item->quantity;
                                                    @endphp
                                                    <small>{{ $item->estimation->no_estimasi }}</small> <br>
                                                    <strong>{{ $item->quantity }} {!! $check ? '' : '<i class="fa fa-check"></i>' !!}<br>
                                                @endforeach
                                            </td>
                                            <td class="text-center px-2"><strong>{{ angka($estimasi) }}</strong></td>
                                            <td class="text-center px-2"><strong>{{ angka($terkirim) }}</strong></td>
                                            <td class="text-center px-2"><strong>{{ angka($sisa) }}</strong></td>
                                            <td class="text-center px-2"><strong>{{ angka($retur) }}</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="text-center px-3" colspan="4"><strong>Total</strong></td>
                                        <td class="text-center px-2"><strong>{{ angka($totalestimasi) }}</strong></td>
                                        <td class="text-center px-2"><strong>{{ angka($totaldikirim) }}</strong></td>
                                        <td class="text-center px-2"><strong>{{ angka($totalsisa) }}</strong></td>
                                        <td class="text-center px-2"><strong>{{ angka($totalretur) }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </section>
            @endforeach


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
