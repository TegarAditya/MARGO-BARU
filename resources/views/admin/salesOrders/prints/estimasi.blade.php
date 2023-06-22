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
    @php
        $total_estimasi = 0;
        $total_sisa = 0;
    @endphp

    <table cellspacing="0" cellpadding="0" class="table table-sm table-bordered" style="width: 100%">
        <thead>
            <tr>
                <th width="1%" class="text-center">No.</th>
                <th>Nama Produk</th>
                <th width="1%" class="text-center">Halaman</th>
                <th width="1%" class="text-center">Estimasi</th>
                <th width="1%" class="text-center">Sisa</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($orders as $order)
                @php
                $sisa = $order->quantity - $order->moved;
                $total_sisa += $sisa;
                $total_estimasi += $order->quantity;

                $product = $order->product;
                @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $product->name }}</td>
                    <td class="text-center">{{ $product->halaman->code }}</td>
                    <td class="text-center">{{ angka($order->quantity) }}</td>
                    <td class="text-center">{{ angka($sisa)}}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th class="text-center" colspan="3"><b>Total</b></th>
                <th class="text-center">{{ angka($total_estimasi) }}</th>
                <th width="1%" class="text-center">{{ angka($total_sisa) }}</th>
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
