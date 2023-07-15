@extends('layouts.print')

@section('header.center')
<h6>SURAT PERINTAH KERJA FINISHING</h6>
@endsection

@section('header.left')
<table cellspacing="0" cellpadding="0" class="text-sm" style="width: 10cm">
    <tbody>

        <tr>
            <td width="200"><strong>No. SPK</strong></td>
            <td width="8">:</td>
            <td>{{ $finishing->no_spk }}</td>
        </tr>

        <tr>
            <td><strong>Tanggal</strong></td>
            <td>:</td>
            <td>{{ $finishing->date }}</td>
        </tr>

        <tr>
            <td><strong>Jenjang</strong></td>
            <td>:</td>
            <td>{{ $finishing->jenjang->name }}</td>
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
            <td>{{ $finishing->vendor->name }}</td>
        </tr>

        <tr>
            <td><strong>Perusahaan</strong></td>
            <td>:</td>
            <td>
                {{ $finishing->vendor->company ?? '-' }}
            </td>
        </tr>

    </tbody>
</table>
@endsection

@section('content')
<table cellspacing="0" cellpadding="0" class="table table-sm table-bordered" style="width: 100%">
    <thead>
        <th width="1%" class="text-center">No.</th>
        {{-- <th>Jenjang</th> --}}
        <th>Cover</th>
        <th>Tema/Mapel</th>
        <th width="1%" class="text-center">Kls</th>
        <th width="1%" class="text-center">Hal</th>
        <th class="text-center" width="10%">Jumlah</th>
    </thead>

    <tbody>
        @php
            $total_item = 0;
        @endphp
        @foreach ($finishing_items as $item)
            @php
            $product = $item->product;
            $total_item += $item->estimasi;
            @endphp
        <tr>
            <td class="px-3">{{ $loop->iteration }}</td>
            {{-- <td>{{ $product->jenjang->name ?? '' }} - {{ $product->kurikulum->code ?? '' }}</td> --}}
            <td>{{ $product->cover->name ?? '' }}</td>
            <td>{{ $product->mapel->name }}</td>
            <td class="text-center">{{ $product->kelas->code ?? '' }}</td>
            <td class="text-center">{{ $product->halaman->code ?? '' }}</td>
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
