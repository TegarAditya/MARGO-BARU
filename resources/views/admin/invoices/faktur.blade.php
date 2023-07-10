@extends('layouts.print')

@section('header.center')
<h6>FAKTUR</h6>
@endsection

@section('header.left')
<table cellspacing="0" cellpadding="0" class="text-sm" style="width: 10cm">
    <tbody>
        <tr>
            <td width="240"><strong>No. Invoice</strong></td>
            <td width="12">:</td>
            <td>{{ $invoice->no_faktur }}</td>
        </tr>

        <tr>
            <td><strong>Tanggal</strong></td>
            <td>:</td>
            <td>{{ Carbon\Carbon::parse($invoice->date)->format('d-m-Y') }}</td>
        </tr>

        <tr>
            <td><strong>Nama Freelance</strong></td>
            <td>:</td>
            <td>{{ $invoice->salesperson->name }} - {{ $invoice->salesperson->marketing_area->name ??    ''}}</td>
        </tr>
    </tbody>
</table>
@stop

@section('header.right')
<table cellspacing="0" cellpadding="0" class="text-sm" style="width: 10cm">
    <tbody>
        <tr>
            <td><strong>No. Surat Jalan</strong></td>
            <td>:</td>
            <td>{{ $invoice->delivery_order->no_suratjalan }}</td>
        </tr>

        <tr>
            <td><strong>Tanggal SJ</strong></td>
            <td>:</td>
            <td>
                {{ Carbon\Carbon::parse($invoice->delivery_order->date)->format('d-m-Y') }}
            </td>
        </tr>

    </tbody>
</table>
@stop

@section('content')
<table cellspacing="0" cellpadding="0" class="table table-sm table-bordered" style="width: 100%">
    <thead>
        <th width="1%" class="text-center">No.</th>
        <th>Jenjang</th>
        <th>Tema/Mapel</th>
        <th width="1%" class="text-center">Kls</th>
        <th width="1%" class="text-center">Hal</th>
        <th width="10%" class="text-center">Harga</th>
        <th width="1%" class="text-center">Quantity</th>
        <th width="20%" class="text-center">Total</th>
        <th width="15%" class="text-center">Diskon</th>
    </thead>

    <tbody>
        @foreach ($invoice_items as $item)
            @php
            $product = $item->product;
            @endphp
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $product->jenjang->name }} - {{ $product->kurikulum->code }}</td>
                <td>{{ $product->mapel->name }}</td>
                <td class="text-center">{{ $product->kelas->code }}</td>
                <td class="text-center">{{ $product->halaman->code }}</td>
                <td class="text-right">{{ money($item->price) }}</td>
                <td class="text-center">{{ angka($item->quantity)}}</td>
                <td class="text-right">{{ money($item->total) }}</td>
                <td class="text-right">{{ money($item->total_discount) }}</td>
            </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <td colspan="9"><br></td>
        </tr>
        <tr>
            <td colspan="7" class="text-right px-3"><strong>Subtotal</strong></td>
            <td colspan="2" class="text-right px-3"><b>{{ money($invoice->total) }}</b></td>
        </tr>
        <tr>
            <td colspan="7" class="text-right px-3"><strong>Discount</strong></td>
            <td colspan="2" class="text-right px-3"><b>{{ money($invoice->discount) }}</b></td>
        </tr>
        <tr>
            <td colspan="7" class="text-right px-3"><strong>Grand Total</strong></td>
            <td colspan="2" class="text-right px-3"><b>{{ money($invoice->nominal) }}</b></td>
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
