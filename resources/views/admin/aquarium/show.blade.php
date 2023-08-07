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
                        @if (isset($platePrint->vendor))
                        <tr>
                            <th>
                                {{ trans('cruds.platePrint.fields.vendor') }}
                            </th>
                            <td>
                                {{ $platePrint->vendor->full_name ?? '' }}
                            </td>
                        </tr>
                        @endif
                        @if (isset($platePrint->customer))
                        <tr>
                            <th>
                                {{ trans('cruds.platePrint.fields.customer') }}
                            </th>
                            <td>
                                {{ $platePrint->customer ?? '' }}
                            </td>
                        </tr>
                        @endif
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
                                    <th>Nama Mapel</th>
                                    <th class="text-center px-2" width="20%">Plate</th>
                                    <th class="text-center px-2" width="10%">Estimasi</th>
                                    <th class="text-center px-2"  width="10%">Status</th>
                                    <th></th>
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
                                        <td class="text-center px-2">{{ App\Models\PlatePrintItem::STATUS_SELECT[$item->status] ?? '' }}</td>
                                        <td class="text-center px-2">
                                            @if ($item->status == 'created')
                                            <a class="px-1" href="{{ route('admin.aquarium.edit', $item->id) }}" title="Accept Task">
                                                <i class="fas fa-check text-primary fa-lg"></i>
                                            </a>
                                            @elseif ($item->status == 'accepted')
                                                <a class="px-1" href=" {{ route('admin.aquarium.realisasi', $item->id) }}" title="Realisasi Task">
                                                    <i class="fas fa-tasks text-warning fa-lg"></i>
                                                </a>    
                                            @else
                                                <a class="px-1" href=" {{ route('admin.aquarium.realisasi', $item->id) }}" title="Edit Realisasi Task">
                                                    <i class="fas fa-edit text-danger fa-lg"></i>
                                                </a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-center px-3" colspan="3"><strong>Total</strong></td>
                                    <td class="text-center px-2"><strong>{{ angka($totalplate) }}</strong></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </section>

            <section class="border-top py-3">
                <div class="row mb-2">
                    <div class="col">
                        <h6>Daftar Plate Selesai</h6>

                        <p class="mb-0">Total Plate: <strong>{{ $items->where('status', 'done')->count() }}</strong></p>
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
                                    <th class="text-center px-2" width="10%">Estimasi</th>
                                    <th class="text-center px-2" width="10%">Realisasi</th>
                                    <th class="text-center px-2"  width="30%">Note</th>
                                </tr>
                            </thead>

                            <tbody>
                                @php
                                    $totalestimasi = 0;
                                    $totalrealisasi = 0;
                                @endphp
                                @foreach ($items->where('status', 'done') as $item)
                                    @php
                                        $totalestimasi += $item->estimasi;
                                        $totalrealisasi += $item->realisasi;
                                    @endphp
                                    <tr>
                                        <td class="text-right px-3">{{ $loop->iteration }}.</td>
                                        <td class="text-center px-3">{{ $item->product ? $item->product->short_name : $item->product_text }}</td>
                                        <td class="text-center px-2">{{ $item->plate ? $item->plate->name : 'Belum Tahu' }}</td>
                                        <td class="text-center px-2">{{ angka($item->estimasi) }}</td>
                                        <td class="text-center px-2">{{ angka($item->realisasi) }}</td>
                                        <td class="text-center px-2">{{ $item->note }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-center px-3" colspan="3"><strong>Total</strong></td>
                                    <td class="text-center px-2"><strong>{{ angka($totalestimasi) }}</strong></td>
                                    <td class="text-center px-2"><strong>{{ angka($totalrealisasi) }}</strong></td>
                                    <td></td>
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
