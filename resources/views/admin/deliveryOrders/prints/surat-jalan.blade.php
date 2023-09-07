@extends('layouts.print')

@section('header.center')
<h6>SURAT JALAN</h6>
@endsection

@section('header.left')
<table cellspacing="0" cellpadding="0" class="text-sm" style="width: 10cm">
    <tbody>

        <tr>
            <td width="200"><strong>No. Surat Jalan</strong></td>
            <td width="8">:</td>
            <td>{{ $deliveryOrder->no_suratjalan }}</td>
        </tr>

        <tr>
            <td><strong>Tanggal</strong></td>
            <td>:</td>
            <td>{{ $deliveryOrder->date }}</td>
        </tr>

    </tbody>
</table>
@stop

@section('header.right')
<table cellspacing="0" cellpadding="0" class="text-sm" style="width: 10cm">
    <tbody>
        <tr>
            <td><strong>Nama Freelance</strong></td>
            <td>:</td>
            <td>{{ $deliveryOrder->salesperson->name }}</td>
        </tr>

        <tr>
            <td><strong>Area Pemasaran</strong></td>
            <td>:</td>
            <td>
                {{ $deliveryOrder->salesperson->marketing_area->name }}
            </td>
        </tr>

        {{-- <tr>
            <td><strong>Alamat</strong></td>
            <td>:</td>
            <td></td>
        </tr> --}}
    </tbody>
</table>
@endsection

@section('content')
<table cellspacing="0" cellpadding="0" class="table table-sm table-bordered" style="width: 100%">
    <thead>
        <th width="1%" class="text-center">No.</th>
        <th>Jenjang</th>
        <th>Cover</th>
        <th>Tema/Mapel</th>
        <th width="1%" class="text-center">Kelas</th>
        <th width="1%" class="text-center">Hal</th>
        <th class="px-3" width="1%">Jumlah</th>
    </thead>

    <tbody>
        @php
            $total_item = 0;
        @endphp
        @foreach ($delivery_items as $item)
            @php
            $product = $item->product;
            if ($item->quantity <= 0) {
                continue;
            }
            $total_item += $item->quantity;
            @endphp
        <tr>
            <td class="px-3">{{ $loop->iteration }}</td>
            <td>{{ $product->type != 'L' ? App\Models\BookVariant::TYPE_SELECT[$product->type] : '' }} {{ $product->jenjang->name ?? '' }} - {{ $product->kurikulum->code ?? '' }}</td>
            <td>{{ $product->cover->name ?? '' }}</td>
            <td>{{ $product->mapel->name }}</td>
            <td class="text-center">{{ $product->kelas->code ?? '' }}</td>
            <td class="text-center">{{ $product->halaman->code ?? '' }}</td>
            <td class="px-3 text-center">{{ angka($item->quantity) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="6" class="text-center"><strong>TOTAL</strong></th>
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
