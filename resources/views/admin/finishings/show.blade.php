@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.finishing.title') }}
    </div>

    <div class="card-body">
        <div class="model-detail">
            <h6>Order Finishing</h6>
            <section class="py-3" id="modelDetail">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.finishing.fields.no_spk') }}
                            </th>
                            <td>
                                {{ $finishing->no_spk }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.finishing.fields.date') }}
                            </th>
                            <td>
                                {{ $finishing->date }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.finishing.fields.semester') }}
                            </th>
                            <td>
                                {{ $finishing->semester->name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.finishing.fields.vendor') }}
                            </th>
                            <td>
                                {{ $finishing->vendor->name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.finishing.fields.note') }}
                            </th>
                            <td>
                                {{ $finishing->note ?? '-' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Ongkos Cetak
                            </th>
                            <td>
                                {{ money($finishing->total_cost) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <section class="border-top py-3">
                <div class="row mb-2">
                    <div class="col">
                        <h6>Daftar Produk</h6>

                        <p class="mb-0">Total Produk: <strong>{{ $finishing_items->count() }}</strong></p>
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
                                    <th class="text-center px-2" width="1%">SPK</th>
                                    <th class="text-center px-2" width="10%">Ongkos</th>
                                    <th class="text-center px-2" width="1%">Realisasi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @php
                                    $totalestimasi = 0;
                                    $totalrealisasi = 0;
                                @endphp
                                @foreach ($finishing_items as $item)
                                    @php
                                    $product = $item->product;
                                    $totalestimasi += $item->estimasi;
                                    if ($item->done) $totalrealisasi += $item->quantity;

                                    @endphp
                                    <tr>
                                        <td class="text-right px-3">{{ $loop->iteration }}.</td>
                                        <td>{{ $product->name }}</td>
                                        <td class="text-center px-2">{{ $product->halaman->code }}</td>
                                        <td class="text-center px-2">{{ angka($item->estimasi) }}</td>
                                        <td class="text-center px-2">{{ money($item->cost) }}</td>
                                        <td class="text-center px-2">
                                            {{ $item->done ? angka($item->quantity) : 'Not Yet' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-center px-3" colspan="3"><strong>Total</strong></td>
                                    <td class="text-center px-2"><strong>{{ angka($totalestimasi) }}</strong></td>
                                    <td class="text-center px-2"><strong>{{ money($finishing->total_cost) }}</strong></td>
                                    <td class="text-center px-2"><strong>{{ angka($totalrealisasi) }}</strong></td>
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