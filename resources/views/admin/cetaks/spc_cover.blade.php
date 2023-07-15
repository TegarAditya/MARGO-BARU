@extends('layouts.print')

@section('header.center')
<h6>SURAT PERINTAH KERJA {{ strtoupper($cetak->type) }}</h6>
@endsection

@section('header.left')
<table cellspacing="0" cellpadding="0" class="text-sm" style="width: 10cm">
    <tbody>

        <tr>
            <td width="200"><strong>No. SPC</strong></td>
            <td width="8">:</td>
            <td>{{ $cetak->no_spc }}</td>
        </tr>

        <tr>
            <td><strong>Tanggal</strong></td>
            <td>:</td>
            <td>{{ $cetak->date }}</td>
        </tr>

        <tr>
            <td><strong>Jenjang</strong></td>
            <td>:</td>
            <td>{{ $cetak->jenjang->name }}</td>
        </tr>

        <tr>
            <td><strong>{{ ucfirst($cetak->type) }}</strong></td>
            <td>:</td>
            <td>{{ $cetak_items->first()->product->cover->name }}</td>
        </tr>

    </tbody>
</table>
@stop

@section('header.right')
<table cellspacing="0" cellpadding="0" class="text-sm" style="width: 10cm">
    <tbody>
        <tr>
            <td><strong>Nama Vendor</strong></td>
            <td>:</td>
            <td>{{ $cetak->vendor->name }}</td>
        </tr>

        <tr>
            <td><strong>Perusahaan</strong></td>
            <td>:</td>
            <td>
                {{ $cetak->vendor->company }}
            </td>
        </tr>

    </tbody>
</table>
@endsection

@section('content')
<table cellspacing="0" cellpadding="0" class="table table-sm table-bordered" style="width: 100%">
    <thead>
        <th width="1%" class="text-center">No.</th>
        <th>Code</th>
        <th>Tema/Mapel</th>
        <th width="1%" class="text-center">Kls</th>
        <th class="text-center">UK Plate</th>
        <th class="px-2" width="1%">Total</th>
    </thead>

    <tbody>
        @php
            $total_item = 0;
        @endphp
        @foreach ($cetak_items as $item)
            @php
            $product = $item->product;
            $total_item += $item->estimasi;
            @endphp
        <tr>
            <td class="px-3">{{ $loop->iteration }}</td>
            <td>{{ $product->code }}</td>
            <td>{{ $product->mapel->name }}</td>
            <td class="text-center">{{ $product->kelas->code ?? '' }}</td>
            <td class="text-center">{{ $item->plate->name ?? '' }}</td>
            <td class="px-3 text-center">{{ angka($item->estimasi) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="5" class="text-center"><strong>TOTAL</strong></th>
            <th class="text-center"><strong>{{ angka($total_item) }}</strong></th>
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
