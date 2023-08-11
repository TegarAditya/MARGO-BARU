@extends('layouts.print')

@section('header.center')
<h6>SURAT JALAN PLATE</h6>
@endsection

@section('header.left')
<table cellspacing="0" cellpadding="0" class="text-sm" style="width: 15cm">
    <tbody>

        <tr>
            <td width="400"><strong>No. Surat Jalan</strong></td>
            <td width="8">:</td>
            <td>{{ $deliveryPlate->no_suratjalan }}</td>
        </tr>

        <tr>
            <td><strong>Tanggal</strong></td>
            <td>:</td>
            <td>{{ $deliveryPlate->date }}</td>
        </tr>

        @if ($deliveryPlate->vendor)
            <tr>
                <td><strong>Vendor</strong></td>
                <td>:</td>
                <td>{{ $deliveryPlate->vendor->name }}</td>
            </tr>
        @else
            <tr>
                <td><strong>Customer</strong></td>
                <td>:</td>
                <td>{{ $deliveryPlate->customer }}</td>
            </tr>
        @endif

    </tbody>
</table>
@stop

@section('content')
<table cellspacing="0" cellpadding="0" class="table table-sm table-bordered" style="width: 100%">
    <thead>
        <th width="1%" class="text-center">No.</th>
        <th class="text-center">Tema/Mapel</th>
        <th class="text-center">UK Plate</th>
        <th class="text-center" width="1%">Quantity</th>
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
            <td class="px-3">{{ $loop->iteration }}</td>
            <td class="text-center">{{ $item->product ? $item->product->short_name : $item->product_text }}</td>
            <td class="text-center">{{ $item->plate->name }}</td>
            <td class="text-center">{{ angka($item->estimasi) }}</td>
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
@endsection

@section('footer')
<div class="row">
    <div class="col align-self-end">
        <p class="mb-2">Dikeluarkan oleh,</p>
        <p class="mb-0">Margo Mitro Joyo</p>
    </div>

    <div class="col-auto text-center">
        <p class="mb-5">Pengirim</p>
        <p class="mb-0">( _____________ )</p>
    </div>

    <div class="col-auto text-center">
        <p class="mb-5">Penerima</p>
        <p class="mb-0">( _____________ )</p>
    </div>
</div>
@endsection

@push('styles')
<style type="text/css" media="print">
@page {
    size: portrait;
}
</style>
@endpush
