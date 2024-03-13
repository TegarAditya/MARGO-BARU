@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.finishingMasuk.title') }}
    </div>

    <div class="card-body">
        <div class="model-detail">
            <h6>Order Finishing</h6>
            <section class="py-3" id="modelDetail">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th width="30%">
                                No SPK (Dari SJ Vendor)
                            </th>
                            <td>
                                {{ $finishingMasuk->no_spk }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Tanggal SPK (Dari SJ Vendor)
                            </th>
                            <td>
                                {{ $finishingMasuk->date }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.finishingMasuk.fields.semester') }}
                            </th>
                            <td>
                                {{ $finishingMasuk->semester->name }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.finishingMasuk.fields.vendor') }}
                            </th>
                            <td>
                                {{ $finishingMasuk->vendor->name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.finishingMasuk.fields.created_by') }}
                            </th>
                            <td>
                                {{ $finishingMasuk->created_by->name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.finishingMasuk.fields.updated_by') }}
                            </th>
                            <td>
                                {{ $finishingMasuk->updated_by->name ?? '' }}
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
                                    <th class="text-center px-2">Buku Masuk</th>
                                    <th class="text-center px-2">SPK / Realisasi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @php
                                    $totalestimasi = 0;
                                    $totalrealisasi = 0;
                                    $totalinput = 0;
                                @endphp
                                @foreach ($finishing_items as $item)
                                    @php
                                    $product = $item->product;
                                    $totalestimasi += $item->finishing_item->estimasi;
                                    $totalrealisasi += $item->finishing_item->quantity;
                                    $totalinput += $item->quantity;
                                    @endphp
                                    <tr>
                                        <td class="text-right px-3">{{ $loop->iteration }}.</td>
                                        <td>{{ $product->name }}</td>
                                        <td class="text-center px-2">{{ $product->halaman->code ?? null }}</td>
                                        <td class="text-center px-2">{{ angka($item->quantity) }}</td>
                                        <td class="text-center px-2">{{ angka($item->finishing_item->estimasi) }} / {{ angka($item->finishing_item->quantity) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-center px-3" colspan="3"><strong>Total</strong></td>
                                    <td class="text-center px-2"><strong>{{ angka($totalinput) }}</strong></td>
                                    <td class="text-center px-2"><strong>{{ angka($totalestimasi) }} / {{ angka($totalrealisasi) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </section>
        </div>
        <div class="row mt-3">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.finishing-masuks.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection
