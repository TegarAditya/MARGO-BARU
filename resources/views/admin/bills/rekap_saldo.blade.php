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
<!-- @if($bills->count() > 0)
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
                    <td class="text-center"><strong>{{ money($bill->piutang_semester) }}</strong></td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td colspan="2" class="text-center"><strong>Total</strong></td>
                <td class="text-center"><strong>{{ money($bills->sum('piutang_semester')) }}</strong></td>
            </tr>
        </tfoot>
    </table>
    <br>
    <div class="my-2 mb-2 ml-5 text-right">
        <p class="m-0">Total Saldo Hutang</p>
        <h5 class="m-0">{{ money($bills->sum('piutang_semester')) }}</h5>
    </div>
    <hr class="my-3 text-right mx-0" />
@endif -->

@if($new_bills->count() > 0)
    <h5 class="mb-3">Saldo Hutang</h5>
    <table cellspacing="0" cellpadding="0" class="table table-sm table-bordered mt-2" style="width: 100%">
        <thead>
            <th width="1%" class="text-center">No.</th>
            <th class="text-center">Semester</th>
            <th class="text-center" width="35%">Saldo Hutang</th>
        </thead>

        <tbody>
            @foreach ($new_bills as $bill)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}.</td>
                    <td class="text-center">{{ $bill->semester_name }}</td>
                    <td class="text-center"><strong>{{ money($bill->saldo_akhir) }}</strong></td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td colspan="2" class="text-center"><strong>Total</strong></td>
                <td class="text-center"><strong>{{ money($new_bills->sum('saldo_akhir')) }}</strong></td>
            </tr>
        </tfoot>
    </table>
    <br>
    <div class="my-2 mb-2 ml-5 text-right">
        <p class="m-0">Total Saldo Hutang</p>
        <h5 class="m-0">{{ money($new_bills->sum('saldo_akhir')) }}</h5>
    </div>
    <hr class="my-3 text-right mx-0" />
@endif


{{-- @if($invoices->count() > 0) --}}
<hr class="my-3 text-right mx-0" />
<h5 class="mb-3">Faktur Penjualan Buku Baru</h5>
<table cellspacing="0" cellpadding="0" class="table table-sm table-bordered mt-2" style="width: 100%">
    <thead>
        <th width="1%" class="text-center">No.</th>
        <th class="text-center">No. Faktur</th>
        <th class="text-center">No. Surat Jalan</th>
        <th class="text-center">Tanggal</th>
        <th width="20%" class="text-center">Subtotal</th>
        <th width="15%" class="text-center">Discount</th>
        <th width="20%" class="text-center">Grand Total</th>
    </thead>

    <tbody>
        @forelse ($invoices->where('type', '!=', 'jual_lama')->sortBy('type') as $invoice)
            <tr>
                <td class="text-center">{{ $loop->iteration }}.</td>
                <td>
                    <div class="row text-center">
                        <div class="col">
                            <span>{{ $invoice->no_faktur }}</span>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('admin.invoices.print-faktur', $invoice->id) }}" class="fa fa-print ml-1 text-info" title="Print Invoice" target="_blank"></a>
                        </div>
                    </div>
                </td>
                <td class="text-center">
                    @if($invoice->type == 'jual')
                        {{ $invoice->delivery_order->no_suratjalan }}
                    @else
                        {{ App\Models\Invoice::TYPE_SELECT[$invoice->type] }}
                    @endif
                </td>
                <td class="text-center">{{ $invoice->date }}</td>
                <td class="text-right">{{ money($invoice->total) }}</td>
                <td class="text-right">{{ money($invoice->discount) }}</td>
                <td class="text-right">{{ money($invoice->nominal) }}</td>
            </tr>
        @empty
            <tr>
                <td class="text-center" colspan="7">Belum ada Faktur</td>
            </tr>
        @endforelse
    </tbody>

    <tfoot>
        <tr>
            <td class="text-center" colspan="4">
                <strong>Total</strong>
            </td>
            <td class="text-right">
                <strong>{{ money($invoices->where('type', '!=', 'jual_lama')->sum('total')) }}</strong>
            </td>
            <td class="text-right">
                <strong>{{ money($invoices->where('type', '!=', 'jual_lama')->sum('discount')) }}</strong>
            </td>
            <td class="text-right">
                <strong>{{ money($invoices->where('type', '!=', 'jual_lama')->sum('nominal')) }}</strong>
            </td>
        </tr>
    </tfoot>
</table>
{{-- @endif --}}

@if($invoices->where('type', 'jual_lama')->count() > 0)
<hr class="my-3 text-right mx-0" />
<h5 class="mb-3">Faktur Penjualan Buku Lama</h5>
<table cellspacing="0" cellpadding="0" class="table table-sm table-bordered mt-2" style="width: 100%">
    <thead>
        <th width="1%" class="text-center">No.</th>
        <th class="text-center">No. Faktur</th>
        <th class="text-center">No. Surat Jalan</th>
        <th class="text-center">Tanggal</th>
        <th width="20%" class="text-center">Subtotal</th>
        <th width="15%" class="text-center">Discount</th>
        <th width="20%" class="text-center">Grand Total</th>
    </thead>

    <tbody>
        @foreach ($invoices->where('type', 'jual_lama') as $invoice)
            <tr>
                <td class="text-center">{{ $loop->iteration }}.</td>
                <td>
                    <div class="row text-center">
                        <div class="col">
                            <span>{{ $invoice->no_faktur }}</span>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('admin.invoices.print-faktur', $invoice->id) }}" 
                               class="fa fa-print ml-1 text-info" 
                               title="Print Invoice" 
                               target="_blank"></a>
                        </div>
                    </div>
                </td>
                <td class="text-center">{{ $invoice->delivery_order->no_suratjalan }}</td>
                <td class="text-center">{{ $invoice->date }}</td>
                <td class="text-right">{{ money($invoice->total) }}</td>
                <td class="text-right">{{ money($invoice->discount) }}</td>
                <td class="text-right">{{ money($invoice->nominal) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td class="text-center" colspan="4"><strong>Total</strong></td>
            <td class="text-right"><strong>{{ money($invoices->where('type', 'jual_lama')->sum('total')) }}</strong></td>
            <td class="text-right"><strong>{{ money($invoices->where('type', 'jual_lama')->sum('discount')) }}</strong></td>
            <td class="text-right"><strong>{{ money($invoices->where('type', 'jual_lama')->sum('nominal')) }}</strong></td>
        </tr>
    </tfoot>
</table>
@endif

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
                    <td class="text-center" colspan="4">Belum ada Adjustment</td>
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

{{-- @if ($returs->count() > 0) --}}
<hr class="my-3 text-right mx-0" />
<h5 class="mb-3">Faktur Retur</h5>
<table cellspacing="0" cellpadding="0" class="table table-sm table-bordered mt-2" style="width: 100%">
    <thead>
        <th width="1%" class="text-center">No.</th>
        <th class="text-center">No. Faktur Retur</th>
        <th class="text-center">Tanggal</th>
        <th width="25%" class="text-center">Nominal</th>
    </thead>

    <tbody>
        @forelse ($returs as $retur)
            <tr>
                <td class="text-center">{{ $loop->iteration }}.</td>
                <td class="text-center">{{ $retur->no_retur }}</td>
                <td class="text-center">{{ $retur->date }}</td>
                <td class="text-right">{{ money($retur->nominal) }}</td>
            </tr>
        @empty
            <tr>
                <td class="text-center" colspan="4">Belum ada Retur</td>
            </tr>
        @endforelse
    </tbody>

    <tfoot>
        <tr>
            <td class="text-center" colspan="3">
                <strong>Total</strong>
            </td>
            <td class="text-right">
                <strong>{{ money($returs->sum('nominal')) }}</strong>
            </td>
        </tr>
    </tfoot>
</table>
{{-- @endif --}}

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
            <td class="text-center" colspan="4">
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
    $saldo_sebelumnya = $new_bills->sum('saldo_akhir');
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
