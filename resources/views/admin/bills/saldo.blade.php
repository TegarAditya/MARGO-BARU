@extends('layouts.print')

@section('header.center')
<h6>Saldo Billing</h6>
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
    </tbody>
</table>
@stop

@section('content')
<h5 class="text-center">SALDO BILLING {{ $semester->name }}</h5>
<br>
@if ($invoices->count() > 0)
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

@if($payments->count() > 0)
    <hr class="my-3 text-right mx-0" />
    <h5 class="mb-3">Pembayaran</h5>
    <table cellspacing="0" cellpadding="0" class="table table-sm table-bordered mt-2" style="width: 100%">
        <thead>
            <th width="1%" class="text-center">No.</th>
            <th class="text-center">No. Kwitansi</th>
            <th class="text-center">Tanggal</th>
            <th width="25%" class="text-center">Bayar</th>
            <th width="20%" class="text-center">Diskon</th>
        </thead>

        <tbody>
            @forelse ($payments as $pembayaran)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}.</td>
                    <td class="text-center">{{ $pembayaran->no_kwitansi }}</td>
                    <td class="text-center">{{ $pembayaran->date }}</td>
                    <td class="text-right">{{ money($pembayaran->paid) }}</td>
                    <td class="text-right">{{ money($pembayaran->discount) }}</td>
                </tr>
            @empty
                <tr>
                    <td class="px-3" colspan="6">Belum ada pembayaran</td>
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
@endif

<hr class="my-3 text-right mx-0" />
@php
    $total_faktur = $invoices->sum('total');
    $total_diskon = $invoices->sum('discount');
    $total_retur = $returs->sum('nominal');
    $total_bayar = $payments->sum('paid');
    $total_potongan = $payments->sum('discount');
    $tagihan = $total_faktur - ($total_diskon + $total_retur + $total_bayar + $total_potongan);
@endphp
<h5 class="mb-3">Resume Tagihan</h5>
<div class="row text-right">
    <div class="col text-left">
        <table class="m-0" style="border: none" width="560px">
            <tbody>
                <tr>
                    <td class="text-left" width="40%"><strong>Saldo Semester Lalu</strong></td>
                    <td>:</td>
                    <td class="text-right"><strong>{{ money($billing->saldo_awal) }}</strong></td>
                </tr>
                <tr>
                    <td class="text-left" width="40%"><strong>Total Faktur Penjualan</strong></td>
                    <td>:</td>
                    <td class="text-right"><strong>{{ money($total_faktur) }}</strong></td>
                </tr>
                <tr>
                    <td class="text-left"><strong>Total Diskon</strong></td>
                    <td>:</td>
                    <td class="text-right"><strong>{{ money($total_diskon) }}</strong></td>
                </tr>
                <tr>
                    <td class="text-left"><strong>Total Faktur Retur</strong></td>
                    <td>:</td>
                    <td class="text-right"><strong>{{ money($total_retur) }}</strong></td>
                </tr>
                <tr>
                    <td class="text-left"><strong>Total Pembayaran</strong></td>
                    <td>:</td>
                    <td class="text-right"><strong>{{ money($total_bayar) }}</strong></td>
                </tr>
                <tr>
                    <td class="text-left"><strong>Total Potongan</strong></td>
                    <td>:</td>
                    <td class="text-right"><strong>{{ money($total_potongan) }}</strong></td>
                </tr>
                <tr>
                    <td class="text-left"><strong>Sisa Tagihan</strong></td>
                    <td>:</td>
                    <td class="text-right"><strong>{{ money($tagihan) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="col-auto">
        <p class="mb-0">
            <span>Saldo Tagihan</span>
            <br />
            <span class="h5 mb-0 tagihan-total font-weight-bold">{{ money($tagihan) }}</span>
        </p>
    </div>
</div>
@foreach ($bills as $bill)
    <hr class="my-4 text-right mx-0" />
    <h5 class="text-center">SALDO BILLING {{ $bill->semester->name }}</h5>
    @php
        $fakturs = $invoices_old->where('semester_id', $bill->semester_id);
        $returs = $returs_old->where('semester_id', $bill->semester_id);
        $payments = $payments_old->where('semester_id', $bill->semester_id);
    @endphp

    @if($fakturs->count() > 0)
        <h5 class="mb-3">Faktur Penjualan</h5>
        <table cellspacing="0" cellpadding="0" class="table table-sm table-bordered mt-2" style="width: 100%">
            <thead>
                <th width="1%" class="text-center">No.</th>
                <th class="text-center">No. Faktur Penjualan</th>
                <th class="text-center">Tanggal</th>
                <th class="text-center">Total Eksemplar</th>
                <th width="25%" class="text-center">Total</th>
                <th width="20%" class="text-center">Diskon</th>
            </thead>

            <tbody>
                @php
                    $totaleksemplar = 0;
                @endphp
                @forelse ($fakturs as $item)
                    @php
                        $detail = $item->invoice_items;
                        $totaleksemplar += $detail ? $detail->sum('quantity') : 0;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}.</td>
                        <td class="text-center">{{ $item->no_faktur }}</td>
                        <td class="text-center">{{ $item->date }}</td>
                        <td class="text-center">{{ $detail ? angka($detail->sum('quantity')) : '-' }}</td>
                        <td class="text-right">{{ money($item->total) }}</td>
                        <td class="text-right">{{ money($item->discount) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-3" colspan="6">Tidak ada Faktur Penjualan</td>
                    </tr>
                @endforelse
            </tbody>

            <tfoot>
                <tr>
                    <td class="text-center" colspan="3"><strong>Total</strong></td>
                    <td class="text-center"><strong>{{ angka($totaleksemplar) }}</strong></td>
                    <td class="text-right px-2"><strong>{{ money($fakturs->sum('total')) }}</strong></td>
                    <td class="text-right px-2"><strong>{{ money($fakturs->sum('discount')) }}</strong></td>
                </tr>
            </tfoot>
        </table>
        <br>
    @endif

    @if($returs->count() > 0)
        <hr class="my-3 text-right mx-0" />
        <h5 class="mb-3">Faktur Retur</h5>
        <table cellspacing="0" cellpadding="0" class="table table-sm table-bordered mt-2" style="width: 100%">
            <thead>
                <th width="1%" class="text-center">No.</th>
                <th class="text-center">No. Faktur Retur</th>
                <th class="text-center">Tanggal</th>
                <th class="text-center">Total Eksemplar</th>
                <th width="25%" class="text-center">Total</th>
            </thead>

            <tbody>
                @php
                    $totaleksemplar = 0;
                @endphp
                @forelse ($returs as $item)
                    @php
                        $detail = $item->retur_items;
                        $totaleksemplar += $detail->sum('quantity');
                    @endphp
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}.</td>
                        <td class="text-center">{{ $item->no_retur }}</td>
                        <td class="text-center">{{ $item->date }}</td>
                        <td class="text-center">{{ angka($detail->sum('quantity')) }}</td>
                        <td class="text-right">{{ money($item->nominal) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-3" colspan="5">Tidak ada Faktur Retur</td>
                    </tr>
                @endforelse
            </tbody>

            <tfoot>
                <tr>
                    <td class="text-center" colspan="3"><strong>Total</strong></td>
                    <td class="text-center"><strong>{{ angka($totaleksemplar) }}</strong></td>
                    <td class="text-right px-2"><strong>{{ money($returs->sum('nominal')) }}</strong></td>
                </tr>
            </tfoot>
        </table>
        <br>
    @endif

    @if($payments->count() > 0)
        <hr class="my-3 text-right mx-0" />
        <h5 class="mb-3">Pembayaran</h5>
        <table cellspacing="0" cellpadding="0" class="table table-sm table-bordered mt-2" style="width: 100%">
            <thead>
                <th width="1%" class="text-center">No.</th>
                <th class="text-center">No. Kwitansi</th>
                <th class="text-center">Tanggal</th>
                <th width="25%" class="text-center">Bayar</th>
                <th width="20%" class="text-center">Diskon</th>
            </thead>

            <tbody>
                @forelse ($payments as $pembayaran)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}.</td>
                        <td class="text-center">{{ $pembayaran->no_kwitansi }}</td>
                        <td class="text-center">{{ $pembayaran->date }}</td>
                        <td class="text-right">{{ money($pembayaran->paid) }}</td>
                        <td class="text-right">{{ money($pembayaran->discount) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-3" colspan="6">Belum ada pembayaran</td>
                    </tr>
                @endforelse
            </tbody>

            <tfoot>
                <tr>
                    <td class="text-center" colspan="3"><strong>Total</strong></td>
                    <td class="text-right px-2"><strong>{{ money($payments->sum('paid')) }}</strong></td>
                    <td class="text-right px-2"><strong>{{ money($payments->sum('discount')) }}</strong></td>
                </tr>
            </tfoot>
        </table>
        <br>
    @endif

    <hr class="my-3 text-right mx-0" />
    @php
        $total_faktur = $invoices->sum('total');
        $total_diskon = $invoices->sum('discount');
        $total_retur = $returs->sum('nominal');
        $total_bayar = $payments->sum('paid');
        $total_potongan = $payments->sum('discount');
        $tagihan = $total_faktur - ($total_diskon + $total_retur + $total_bayar + $total_potongan);
    @endphp
    <h5 class="mb-3">Resume Tagihan</h5>
    <div class="row text-right">
        <div class="col text-left">
            <table class="m-0" style="border: none" width="560px">
                <tbody>
                    <tr>
                        <td class="text-left" width="40%"><strong>Total Faktur Penjualan</strong></td>
                        <td>:</td>
                        <td class="text-right"><strong>{{ money($bill->jual) }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-left"><strong>Total Diskon</strong></td>
                        <td>:</td>
                        <td class="text-right"><strong>{{ money($bill->diskon) }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-left"><strong>Total Faktur Retur</strong></td>
                        <td>:</td>
                        <td class="text-right"><strong>{{ money($bill->retur) }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-left"><strong>Total Pembayaran</strong></td>
                        <td>:</td>
                        <td class="text-right"><strong>{{ money($bill->bayar) }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-left"><strong>Total Potongan</strong></td>
                        <td>:</td>
                        <td class="text-right"><strong>{{ money($bill->potongan) }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-left"><strong>Sisa Tagihan</strong></td>
                        <td>:</td>
                        <td class="text-right"><strong>{{ money($bill->saldo_akhir) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="col-auto">
            <p class="mb-0">
                <span>Saldo Tagihan</span>
                <br />
                <span class="h5 mb-0 tagihan-total font-weight-bold">{{ money($bill->saldo_akhir) }}</span>
            </p>
        </div>
    </div>
@endforeach

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
