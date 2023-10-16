@extends('layouts.admin')
@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1 class="m-0 bold">Estimasi Sales</h1>
    </div>
</div>
<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.estimation.title') }}
    </div>

    <div class="card-body">
        <div class="model-detail">
            <section class="py-3" id="modelDetail">
                <table class="table table-sm border m-0">
                    <tbody>
                        <tr>
                            <th width="150">
                                No Estimasi
                            </th>
                            <td>
                                {{ $estimation->no_estimasi ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th width="150">
                                Tanggal
                            </th>
                            <td>
                                {{ $estimation->date ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Sales
                            </th>
                                <td>
                                    {{ $estimation->salesperson->name ?? 'Internal' }}
                                </td>
                        </tr>
                        <tr>
                            <th width="150">
                                Semester
                            </th>
                            <td>
                                {{ $estimation->semester->name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th width="150">
                                Dibuat Oleh
                            </th>
                            <td>
                                {{ $estimation->created_by->name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th width="150">
                                Diedit Oleh
                            </th>
                            <td>
                                {{ $estimation->updated_by->name ?? '' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>
            <section class="border-top py-3">
                <div class="row mb-2">
                    <div class="col">
                        <h6>Daftar Estimasi</h6>

                        <p class="mb-0">Total Produk: {{ $estimasi_list->count() }}</p>
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
                                </tr>
                            </thead>

                            <tbody>
                                @php
                                    $totalestimasi = 0;
                                @endphp
                                @foreach ($estimasi_list as $item)
                                    @php
                                    $product = $item->product;
                                    $totalestimasi += $item->quantity;
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
                                    <td class="text-center px-2"><strong>{{ angka($totalestimasi) }}</strong></td>
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
