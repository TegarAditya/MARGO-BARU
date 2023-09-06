@extends('layouts.print')

@section('header.center')
<h6>SALDO BILLING {{ $semester->name }}</h6>
@stop

@section('header.left')
<table cellspacing="0" cellpadding="0" class="text-sm" style="width: 10cm">
    <tbody>
        <tr>
            <td><strong>Nama Freelance</strong></td>
            <td>:</td>
            <td>{{ $salesperson->name }}</td>
        </tr>

        <tr>
            <td><strong>Area Pemasaran</strong></td>
            <td>:</td>
            <td>
                {{ $salesperson->marketing_area->name }}
            </td>
        </tr>
        <tr>
            <td><strong>Tanggal</strong></td>
            <td>:</td>
            <td>
                {{ Carbon\Carbon::now()->format('d-m-Y') }}
            </td>
        </tr>
    </tbody>
</table>
@stop

@section('content')
@if($bills->count() > 0)
    <h5 class="mb-3">Saldo Hutang</h5>
    <table cellspacing="0" cellpadding="0" class="table table-sm table-bordered mt-2" style="width: 100%">
        <thead>
            <th width="1%" class="text-center">No.</th>
            <th class="text-center">Semester</th>
            <th class="text-center" width="35%">Saldo Hutang</th>
        </thead>

        <tbody>
            @foreach ($bills as $bill)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}.</td>
                    <td class="text-center">{{ $bill->semester->name }}</td>
                    <td class="text-center"><strong>{{ money($bill->saldo_akhir) }}</strong></td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td colspan="2" class="text-center"><strong>Total</strong></td>
                <td class="text-center"><strong>{{ money($bills->sum('saldo_akhir')) }}</strong></td>
            </tr>
        </tfoot>
    </table>
    <br>
    <div class="my-2 mb-2 ml-5 text-right">
        <p class="m-0">Total Saldo Hutang</p>
        <h5 class="m-0">{{ money($bills->sum('saldo_akhir')) }}</h5>
    </div>
    <hr class="my-3 text-right mx-0" />
@endif

<h5 class="mb-3">Faktur Penjualan</h5>
@foreach ($invoices as $invoice)
    @if($invoice->type == 'jual')
        <div class="row">
            <div class="col-6">
                <p class="mb-0 text-sm">
                    No. Faktur Penjualan
                    <br />
                    <strong>{{ $invoice->no_faktur }}</strong>
                </p>
            </div>
            <div class="col-6">
                <p class="mb-0 text-sm">
                    Tanggal
                    <br />
                    <strong>{{ Carbon\Carbon::parse($invoice->date)->format('d-m-Y') }}</strong>
                </p>
            </div>
        </div>
        <table cellspacing="0" cellpadding="0" class="table table-sm table-bordered" style="width: 100%">
            <thead>
                <th width="1%" class="text-center">No.</th>
                <th class="text-center">Jenjang</th>
                <th class="text-center">Tema/Mapel</th>
                {{-- <th width="1%" class="text-center">Hal</th> --}}
                <th width="10%" class="text-center">Harga</th>
                <th width="1%" class="text-center">Quantity</th>
                <th width="17%" class="text-center">Total</th>
                <th width="13%" class="text-center">Diskon</th>
            </thead>

            <tbody>
                @foreach ($invoice->invoice_items as $item)
                    @php
                    $product = $item->product;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}.</td>
                        <td class="text-center">{{ $product->jenjang->name }} - {{ $product->kurikulum->code }}</td>
                        <td class="text-center">{{ $product->short_name }}</td>
                        {{-- <td class="text-center">{{ $product->halaman->code }}</td> --}}
                        <td class="text-right">{{ money($item->price )}}</td>
                        <td class="text-center">{{ angka($item->quantity) }}</td>
                        <td class="text-right">{{ money($item->total) }}</td>
                        <td class="text-right">{{ money($item->total_discount) }}</td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="3" class="text-right px-3"><strong>Total Eksemplar</strong></td>
                    <td colspan="4" class="text-right px-3"><b>{{ angka($invoice->invoice_items->sum('quantity')) }}</b></td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right px-3"><strong>Subtotal</strong></td>
                    <td colspan="4" class="text-right px-3"><b>{{ money($invoice->total) }}</b></td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right px-3"><strong>Discount</strong></td>
                    <td colspan="4" class="text-right px-3"><b>{{ money($invoice->discount) }}</b></td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right px-3"><strong>Grand Total</strong></td>
                    <td colspan="4" class="text-right px-3"><b>{{ money($invoice->nominal) }}</b></td>
                </tr>
            </tfoot>
        </table>
    @else
        <div class="row">
            <div class="col-6">
                <p class="mb-0 text-sm">
                    No. Faktur
                    <br />
                    <strong>{{ $invoice->no_faktur }}</strong>
                </p>
            </div>
            <div class="col-6">
                <p class="mb-0 text-sm">
                    Tanggal
                    <br />
                    <strong>{{ Carbon\Carbon::parse($invoice->date)->format('d-m-Y') }}</strong>
                </p>
            </div>
        </div>
        <table class="table table-sm table-bordered m-0">
            <thead>
                <tr>
                    <th class="text-center">Keperluan</th>
                    <th  class="text-center">Catatan</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td class="text-center">{{ App\Models\Invoice::TYPE_SELECT[$invoice->type] }}</td>
                    <td class="text-center">{{ $invoice->note }}</td>
                </tr>
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="2"><br></td>
                </tr>
                <tr>
                    <td class="text-center"><strong>Total</strong></td>
                    <td class="text-center px-5"><b>{{ money($invoice->nominal) }}</b></td>
                </tr>
            </tfoot>
        </table>
    @endif
    <br>
@endforeach
@if($adjustments->count() > 0)
<hr class="my-3 text-right mx-0" />
<h5 class="mb-3">Adjustment</h5>
<table cellspacing="0" cellpadding="0" class="table table-sm table-bordered mt-2" style="width: 100%">
    <thead>
        <th width="1%" class="text-center">No.</th>
        <th class="text-center">No. Adjustment</th>
        <th class="text-center">Tanggal</th>
        <th width="25%" class="text-center">Amount</th>
    </thead>

    <tbody>
        @forelse ($adjustments as $adjustment)
            <tr>
                <td class="text-center">{{ $loop->iteration }}.</td>
                <td class="text-center">{{ $adjustment->no_adjustment }}</td>
                <td class="text-center">{{ $adjustment->date }}</td>
                <td class="text-right">{{ money($adjustment->amount) }}</td>
            </tr>
        @empty
            <tr>
                <td class="text-center" colspan="6">Belum ada Adjustment</td>
            </tr>
        @endforelse
    </tbody>

    <tfoot>
        <tr>
            <td class="text-center" colspan="3">
                <strong>Total</strong>
            </td>
            <td class="text-right">
                <strong>{{ money($adjustments->sum('amount')) }}</strong>
            </td>
        </tr>
    </tfoot>
</table>
@endif

@if ($returs->count() > 0)
    <hr class="my-3 text-right mx-0" />
    <h5 class="mb-3">Faktur Retur</h5>
    @foreach ($returs as $returnGood)
        <div class="row">
            <div class="col-6">
                <p class="mb-0 text-sm">
                    No. Faktur Retur
                    <br />
                    <strong>{{ $returnGood->no_retur }}</strong>
                </p>
            </div>
            <div class="col-3">
                <p class="mb-0 text-sm">
                    Tanggal
                    <br />
                    <strong>{{ Carbon\Carbon::parse($returnGood->date)->format('d-m-Y') }}</strong>
                </p>
            </div>
        </div>
        <table cellspacing="0" cellpadding="0" class="table table-sm table-bordered" style="width: 100%">
            <thead>
                <th width="1%" class="text-center">No.</th>
                <th class="text-center">Jenjang - Kelas</th>
                <th class="text-center">Tema/Mapel</th>
                {{-- <th width="1%" class="text-center">Hal</th> --}}
                <th width="15%" class="text-center">Harga</th>
                <th width="10%" class="text-center">Quantity</th>
                <th width="20%" class="text-center">Total</th>
            </thead>

            <tbody>
                @foreach ($returnGood->retur_items as $item)
                    @php
                        $product = $item->product;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}.</td>
                        <td class="text-center">{{ $product->jenjang->name ?? '' }} - {{ $product->kurikulum->code ?? '' }}</td>
                        <td class="text-center">{{ $product->short_name }}</td>
                        {{-- <td class="text-center">{{ $product->halaman->code }}</td> --}}
                        <td class="text-right">{{ money($item->price )}}</td>
                        <td class="text-center">{{ angka($item->quantity) }}</td>
                        <td class="text-right" width="15%">{{ money($item->total) }}</td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="4" class="text-center"><strong>Total</strong></td>
                    <td class="text-center"><b>{{ angka($returnGood->retur_items->sum('quantity')) }}</b></td>
                    <td class="text-right"><b>{{ money($returnGood->nominal) }}</b></td>
                </tr>
            </tfoot>
        </table>
        <br>
    @endforeach
@endif

@php
    $faktur = $invoices->sum('total');
    $diskon = $invoices->sum('discount');
    $adjustment = $adjustments->sum('amount');
    $retur = $returs->sum('nominal');
    $tagihan = $faktur - ($adjustment + $diskon + $retur);
@endphp
<div class="my-2 mb-2 ml-5 text-right">
    <p class="m-0">Total Tagihan</p>
    <h5 class="m-0">{{ money($tagihan) }}</h5>
</div>

<hr class="my-3 text-right mx-0" />
<h5 class="mb-3">Pembayaran</h5>
<table cellspacing="0" cellpadding="0" class="table table-sm table-bordered mt-2" style="width: 100%">
    <thead>
        <th width="1%" class="text-center">No.</th>
        <th class="text-center">No. Kwitansi</th>
        <th class="text-center">Tanggal</th>
        <th class="text-center">Metode Pembayaran</th>
        <th width="25%" class="text-center">Bayar</th>
        <th width="20%" class="text-center">Diskon</th>
    </thead>

    <tbody>
        @forelse ($payments as $pembayaran)
            <tr>
                <td class="text-center">{{ $loop->iteration }}.</td>
                <td class="text-center">{{ $pembayaran->no_kwitansi }}</td>
                <td class="text-center">{{ $pembayaran->date }}</td>
                <td class="text-center">{{ App\Models\Payment::PAYMENT_METHOD_SELECT[$pembayaran->payment_method] }}</td>
                <td class="text-right">{{ money($pembayaran->paid) }}</td>
                <td class="text-right">{{ money($pembayaran->discount) }}</td>
            </tr>
        @empty
            <tr>
                <td class="text-center" colspan="6">Belum ada pembayaran</td>
            </tr>
        @endforelse
    </tbody>

    <tfoot>
        <tr>
            <td class="text-center" colspan="3">
                <strong>Total</strong>
            </td>
            <td class="text-right">
                <strong>{{ money($payments->sum('paid')) }}</strong>
            </td>
            <td class="text-right">
                <strong>{{ money($payments->sum('discount')) }}</strong>
            </td>
        </tr>
    </tfoot>
</table>
<br>
@php
    $bayar = $payments->sum('paid');
    $potongan = $payments->sum('discount');
    $bayaran = $bayar + $potongan;
@endphp
<div class="my-2 mb-2 ml-5 text-right">
    <p class="m-0">Total Pembayaran</p>
    <h5 class="m-0">{{ money($bayaran) }}</h5>
</div>

<hr class="my-3 text-right mx-0" />
@php
    $saldo_sebelumnya = $bills->sum('saldo_akhir');
    $total_hutang = ($saldo_sebelumnya + $tagihan) - $bayaran;
@endphp
<h5 class="mb-3">Resume</h5>
<div class="row text-right">
    <div class="col text-left">
        <table class="m-0" style="border: none" width="560px">
            <tbody>
                @if($saldo_sebelumnya)
                <tr>
                    <td class="text-left" width="40%">Total Saldo Hutang</td>
                    <td>:</td>
                    <td class="text-right"><strong>{{ money($saldo_sebelumnya) }}</strong></td>
                </tr>
                @endif
                <tr>
                    <td class="text-left" width="40%">Total Tagihan</td>
                    <td>:</td>
                    <td class="text-right"><strong>{{ money($tagihan) }}</strong></td>
                </tr>
                <tr>
                    <td class="text-left">Total Pembayaran</td>
                    <td>:</td>
                    <td class="text-right"><strong>{{ money($bayaran) }}</strong></td>
                </tr>
                <tr>
                    <td class="text-left">Sisa Hutang</td>
                    <td>:</td>
                    <td class="text-right"><strong>{{ money($total_hutang) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="col-auto">
        <p class="mb-0">
            <span><strong>Sisa Hutang</strong></span>
            <br />
            <span class="h5 mb-0 tagihan-total font-weight-bold">{{ money($total_hutang) }}</span>
        </p>
    </div>
</div>

<div class="mt-5"></div>
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
