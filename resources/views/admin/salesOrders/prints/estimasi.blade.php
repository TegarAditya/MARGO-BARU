@extends('layouts.print')

@section('header.center')
<h6>ESTIMASI ORDER</h6>
@endsection

@section('header.left')
<table cellspacing="0" cellpadding="0" class="text-sm" style="width: 10cm">
    <tbody>
        <tr>
            <td><strong>Nama Freelance</strong></td>
            <td>:</td>
            <td>{{ $salesOrder->salesperson->name }}</td>
        </tr>

        <tr>
            <td><strong>Area Pemasaran</strong></td>
            <td>:</td>
            <td>
                {{ $salesOrder->salesperson->marketing_area ? $salesOrder->salesperson->marketing_area->name : ''}}
            </td>
        </tr>

    </tbody>
</table>
@stop

@section('content')
    @foreach ($grouped as $key => $value)
        @if ($loop->first)
            <h5 class="text-center my-3">JENJANG {{ $key }}</h5>
        @else
            <h5 class="pagebreak text-center my-3">JENJANG {{ $key }}</h5>
        @endif

        @php
            $total_sisa = 0;
        @endphp

        <table cellspacing="0" cellpadding="0" class="table table-sm table-bordered" style="width: 100%">
            <thead>
                <tr>
                    <th width="1%" class="text-center">No.</th>
                    <th width="20%">Cover</th>
                    <th>Tema/Mapel</th>
                    <th width="1%">Kelas</th>
                    <th width="1%" class="text-center">Hal</th>
                    <th width="1%" class="text-center">Sisa</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($value as $order)
                    @php
                    $sisa = $order->quantity - $order->moved;
                    $total_sisa += $sisa;

                    $product = $order->product;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $product->cover->name }}</td>
                        <td>{{ $product->mapel->name }}</td>
                        <td class="text-center">{{ $product->kelas->code }}</td>
                        <td class="text-center">{{ $product->halaman->code }}</td>
                        <td class="text-center">{{ angka(max($sisa, 0))}}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th class="text-center" colspan="5"><b>Total</b></th>
                    <th width="1%" class="text-center">{{ angka($total_sisa) }}</th>
                </tr>
            </tfoot>
        </table>
    @endforeach
@endsection

@section('footer')
<div class="row">
    <div class="col align-self-end">
        <p class="mb-2">Dikeluarkan oleh,</p>
        <p class="mb-0">Margo Mitro Joyo</p>
    </div>

    {{-- <div class="col-auto text-center">
        <p class="mb-5">Pengirim</p>
        <p class="mb-0">( _____________ )</p>
    </div>

    <div class="col-auto text-center">
        <p class="mb-5">Penerima</p>
        <p class="mb-0">( _____________ )</p>
    </div> --}}
</div>
@endsection

@push('styles')
<style type="text/css" media="print">
@page {
    size: portrait;
}
</style>
@endpush
