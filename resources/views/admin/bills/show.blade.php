@extends('layouts.admin')
@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1 class="m-0">Billing</h1>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <strong>BILLING #{{ $salesperson->full_name }} #{{ $semester->name }}</strong>
    </div>

    <div class="card-body">
        <div class="model-detail mt-3">
            <h5>#{{ $salesperson->full_name }} #{{ $semester->name }}</h5>

            <div class="breadcrumb-nav">
                <ul class="m-0 border-bottom">
                    @if($bills->count() > 0)
                        <li><a href="#modelTagihan">Saldo Hutang</a></li>
                    @endif
                    <li><a href="#modelInvoice">Faktur Penjualan</a></li>
                    <li><a href="#modelRetur">Faktur Retur</a></li>
                    <li><a href="#modelBayar">Pembayaran</a></li>
                    <li><a href="#modelResume">Resume Tagihan</a></li>
                    @foreach ($bills as $item)
                        <li><a href="#modelSemester{{ $item->semester_id }}">{{ $item->semester->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            @if($bills->count() > 0)
                {{-- Saldo Sebelumnya --}}
                <section class="border-top py-3 mt-5" id="modelTagihan">
                    <div class="row mb-2">
                        <div class="col">
                            <h6>Daftar Saldo Hutang</h6>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered table-hover m-0">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="1%">No.</th>
                                        <th class="text-center">Semester</th>
                                        <th class="text-center" width="35%">Saldo Hutang</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($bills as $bill)
                                        <tr>
                                            <td class="text-right px-3">{{ $loop->iteration }}.</td>
                                            <td class="text-center">{{ $bill->semester->name }}</td>
                                            <td class="text-center"><strong>{{ money($bill->saldo_akhir) }}</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <td colspan="2" class="text-center">Total</td>
                                        <td class="text-center"><strong>{{ money($bills->sum('saldo_akhir')) }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </section>
            @endif

            {{-- Invoice --}}
            <section class="border-top py-3" id="modelInvoice">
                <div class="row mb-2">
                    <div class="col">
                        <h6>Daftar Faktur Penjualan</h6>

                        <p class="mb-0">Total invoice: <strong>{{ $invoices->count() }}</strong></p>
                    </div>

                    <div class="col-auto">
                        <a href="{{ route('admin.delivery-orders.create') }}" class="btn btn-sm btn-success">Tambah Surat Jalan</a>
                        <a href="{{ route('admin.invoices.create') }}" class="btn btn-sm btn-secondary">Tambah Faktur</a>
                    </div>
                </div>

                @foreach ($invoices as $invoice)
                    @if($invoice->type == 'jual')
                        <div class="card">
                            <div class="card-body px-3 py-2">
                                <div class="row">
                                    <div class="col-6 mb-1">
                                        <span class="badge badge-warning">Faktur Penjualan</span>
                                    </div>

                                    <div class="col-6 text-right">
                                        <a href="{{ route('admin.invoices.edit', $invoice->id) }}" class="border-bottom">
                                            <i class="fa fa-edit"></i> Edit
                                        </a>
                                    </div>

                                    <div class="col-4">
                                        <p class="mb-0 text-sm">
                                            No. Invoice
                                            <br />
                                            <strong>{{ $invoice->no_faktur }}</strong>

                                            <a href="{{ route('admin.invoices.print-faktur', $invoice->id) }}" class="fa fa-print ml-1 text-info" title="Print Invoice" target="_blank"></a>
                                        </p>
                                    </div>
                                    <div class="col-4">
                                        <p class="mb-0 text-sm">
                                            Tanggal
                                            <br />
                                            <strong>{{ $invoice->date }}</strong>
                                        </p>
                                    </div>

                                    {{-- <div class="col text-right">
                                        <span>Tanggal<br />{{ $invoice->date }}</span>
                                    </div> --}}
                                </div>

                                <div class="row">
                                    <div class="col-4">
                                        <p class="mb-0 text-sm">
                                            Salesman
                                            <br />
                                            <strong>{{ $invoice->salesperson->name }} - {{ $invoice->salesperson->marketing_area->name }} </strong>
                                        </p>
                                    </div>

                                    <div class="col-4">
                                        <p class="mb-0 text-sm">
                                            Semester
                                            <br />
                                            <strong>{{ $invoice->semester->name }}</strong>
                                        </p>
                                    </div>
                                </div>

                                <p class="mt-4 mb-1">
                                    <strong>Produk</strong>
                                </p>

                                <table class="table table-sm table-bordered m-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center" width="1%">No.</th>
                                            <th>Jenjang</th>
                                            <th>Tema/Mapel</th>
                                            <th class="px-2" width="10%">Harga</th>
                                            <th class="px-2" width="10%">Quantity</th>
                                            <th class="px-2" width="17%">Total</th>
                                            <th class="px-2" width="12%">Diskon</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse ($invoice->invoice_items as $item)
                                            @php
                                                $product = $item->product;
                                            @endphp
                                            <tr>
                                                <td class="text-right px-3">{{ $loop->iteration }}.</td>
                                                <td class="text-center">{{ $product->jenjang->name }} - {{ $product->kurikulum->code }}</td>
                                                <td class="text-center px-2">{{ $product->short_name }}</td>
                                                <td class="text-right px-2">{{ money($item->price )}}</td>
                                                <td class="text-center px-2">{{ angka($item->quantity) }}</td>
                                                <td class="text-right px-2">{{ money($item->total) }}</td>
                                                <td class="text-right px-2">{{ money($item->total_discount) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td class="px-3" colspan="6">Tidak ada produk</td>
                                            </tr>
                                        @endforelse
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <td colspan="7"><br></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-right px-5"><strong>Subtotal</strong></td>
                                            <td colspan="4" class="text-right px-5"><b>{{ money($invoice->total) }}</b></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-right px-5"><strong>Discount</strong></td>
                                            <td colspan="4" class="text-right px-5"><b>{{ money($invoice->discount) }}</b></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-right px-5"><strong>Grand Total</strong></td>
                                            <td colspan="4" class="text-right px-5"><b>{{ money($invoice->nominal) }}</b></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="card">
                            <div class="card-body px-3 py-2">
                                <div class="row">
                                    <div class="col-6 mb-1">
                                        <span class="badge badge-warning">Faktur</span>
                                    </div>

                                    <div class="col-6 text-right">
                                        <a href="{{ route('admin.invoices.edit', $invoice->id) }}" class="border-bottom">
                                            <i class="fa fa-edit"></i> Edit
                                        </a>
                                    </div>

                                    <div class="col-4">
                                        <p class="mb-0 text-sm">
                                            No. Invoice
                                            <br />
                                            <strong>{{ $invoice->no_faktur }}</strong>

                                            <a href="{{ route('admin.invoices.print-faktur', $invoice->id) }}" class="fa fa-print ml-1 text-info" title="Print Invoice" target="_blank"></a>
                                        </p>
                                    </div>

                                    <div class="col-4">
                                        <p class="mb-0 text-sm">
                                            Tanggal
                                            <br />
                                            <strong>{{ $invoice->date }} </strong>
                                        </p>
                                    </div>

                                    {{-- <div class="col text-right">
                                        <span>Tanggal<br />{{ $invoice->date }}</span>
                                    </div> --}}
                                </div>

                                <div class="row">
                                    <div class="col-4">
                                        <p class="mb-0 text-sm">
                                            Salesman
                                            <br />
                                            <strong>{{ $invoice->salesperson->name }} - {{ $invoice->salesperson->marketing_area->name }} </strong>
                                        </p>
                                    </div>

                                    <div class="col-4">
                                        <p class="mb-0 text-sm">
                                            Semester
                                            <br />
                                            <strong>{{ $invoice->semester->name }}</strong>
                                        </p>
                                    </div>
                                </div>

                                <p class="mt-4 mb-1">
                                    <strong>Produk</strong>
                                </p>

                                <table class="table table-sm table-bordered m-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center" width="1%">No.</th>
                                            <th class="text-center">Keperluan</th>
                                            <th>Catatan</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr>
                                            <td class="text-right px-3">{{ $loop->iteration }}.</td>
                                            <td class="text-center">{{ App\Models\Invoice::TYPE_SELECT[$invoice->type] }}</td>
                                            <td class="text-center">{{ $invoice->note }}</td>
                                        </tr>
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <td colspan="2"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center"><strong>Total</strong></td>
                                            <td class="text-right px-5"><b>{{ money($invoice->nominal) }}</b></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endif
                @endforeach
                <hr class="my-2 text-right ml-5 mx-0" />

                <div class="row text-right">
                    <div class="col text-left">

                    </div>

                    <div class="col-auto">
                        <p class="mb-0">
                            <span>Total Invoice</span>
                            <br />
                            <span class="h5 mb-0 invoice-total"><strong>{{ money($invoices->sum('total')) }}</strong></span>
                        </p>
                    </div>

                    <div class="col-auto">
                        <p class="mb-0">
                            <span>Total Discount</span>
                            <br />
                            <span class="h5 mb-0 invoice-diskon"><strong>{{ money($invoices->sum('discount')) }}</strong></span>
                        </p>
                    </div>
                </div>

                @if($adjustments->count() > 0)
                    <div class="row mb-2 border-top py-3 mt-5">
                        <div class="col">
                            <h6>Adjustment</h6>

                            <p class="mb-0">Total Adjustment: {{ $adjustments->count() }}</p>
                        </div>

                        <div class="col-auto">
                            <a href="{{ route('admin.bill-adjustments.create') }}" class="btn btn-sm btn-success">Tambah Adjustment</a>
                        </div>
                    </div>

                    <table class="table table-bordered table-hover m-0">
                        <thead>
                            <tr>
                                <th class="text-center" width="1%">No.</th>
                                <th>No. Adjustment</th>
                                <th class="text-center px-3" width="100">Tanggal</th>
                                <th class="text-center px-3">Semester</th>
                                <th class="text-center px-3" width="20%">Amount</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($adjustments as $adjustment)
                                <tr>
                                    <td class="text-right px-3">{{ $loop->iteration }}.</td>
                                    <td>
                                        <div class="row text-center">
                                            <div class="col">
                                                <span>{{ $adjustment->no_adjustment }}</span>
                                                {{-- <a href="{{ route('admin.payments.kwitansi', $pembayaran->id) }}" title="Cetak Pembayaran" target="_blank" class="text-info ml-1">
                                                    <i class="fa fa-print"></i>
                                                </a> --}}
                                            </div>

                                            <div class="col-auto">
                                                <a href="{{ route('admin.bill-adjustments.edit', $adjustment->id) }}" title="Edit Adjustment" class="text-info ml-1">
                                                    <i class="fa fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center px-2">{{ $adjustment->date }}</td>
                                    <td class="text-center">{{ $adjustment->semester->name }}</td>
                                    <td class="text-right px-3">{{ money($adjustment->amount) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-3" colspan="5">Belum ada Adjustment</td>
                                </tr>
                            @endforelse
                        </tbody>

                        <tfoot>
                            <tr>
                                <td class="text-center px-3" colspan="4">
                                    <strong>Total</strong>
                                </td>
                                <td class="text-right px-3">
                                    <strong>{{ money($adjustments->sum('amount')) }}</strong>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                @endif

                <hr class="my-2 text-right ml-5 mx-0" />

                <div class="row text-right">
                    <div class="col text-left">

                    </div>

                    <div class="col-auto">
                        <p class="mb-0">
                            <span>Total Adjustment</span>
                            <br />
                            <span class="h5 mb-0 invoice-total"><strong>{{ money($adjustments->sum('amount')) }}</strong></span>
                        </p>
                    </div>
                </div>
            </section>

            {{-- Retur --}}
            <section class="border-top py-3 mt-5" id="modelRetur">
                <div class="row mb-2">
                    <div class="col">
                        <h6>Daftar Faktur Retur</h6>

                        <p class="mb-0">Total Faktur Retur: {{ $returs->count() }}</p>
                    </div>

                    <div class="col-auto">
                        <a href="{{ route('admin.return-goods.create') }}" class="btn btn-sm btn-success">Tambah Faktur Retur</a>
                    </div>
                </div>

                @foreach ($returs as $returnGood)
                    <div class="card">
                        <div class="card-body px-3 py-2">
                            <div class="row">
                                <div class="col-6 mb-1">
                                    <span class="badge badge-danger">Faktur Retur</span>
                                </div>

                                <div class="col-6 text-right">
                                    <a href="{{ route('admin.return-goods.edit', $returnGood->id) }}" class="border-bottom">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                </div>

                                <div class="col-4">
                                    <p class="mb-0 text-sm">
                                        No. Retur
                                        <br />
                                        <strong>{{ $returnGood->no_retur }}</strong>

                                        <a href="{{ route('admin.return-goods.print-faktur', $returnGood->id) }}" class="fa fa-print ml-1 text-info" title="Print Faktur Retur" target="_blank"></a>
                                    </p>
                                </div>

                                <div class="col-4">
                                    <p class="mb-0 text-sm">
                                        Tanggal
                                        <br />
                                        <strong>{{ $returnGood->date }} </strong>
                                    </p>
                                </div>

                                {{-- <div class="col text-right">
                                    <span>Tanggal<br />{{ $returnGood->date }}</span>
                                </div> --}}
                            </div>

                            <div class="row">
                                <div class="col-4">
                                    <p class="mb-0 text-sm">
                                        Salesman
                                        <br />
                                        <strong>{{ $returnGood->salesperson->name }} - {{ $returnGood->salesperson->marketing_area->name }} </strong>
                                    </p>
                                </div>

                                <div class="col-4">
                                    <p class="mb-0 text-sm">
                                        Semester
                                        <br />
                                        <strong>{{ $returnGood->semester->name }}</strong>
                                    </p>
                                </div>
                            </div>

                            <p class="mt-4 mb-1">
                                <strong>Produk</strong>
                            </p>

                            <table class="table table-sm table-bordered m-0">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="1%">No.</th>
                                        <th>Jenjang</th>
                                        <th>Tema/Mapel</th>
                                        <th class="px-2">Harga</th>
                                        <th class="px-2">Quantity</th>
                                        <th class="px-2">Total</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($returnGood->retur_items as $item)
                                        @php
                                            $product = $item->product;
                                        @endphp
                                        <tr>
                                            <td class="text-right px-3">{{ $loop->iteration }}.</td>
                                            <td class="text-center">{{ $product->jenjang->name ?? '' }} - {{ $product->kurikulum->code ?? '' }}</td>
                                            <td>{{ $product->short_name }}</td>
                                            <td class="text-center px-2" width="10%">{{ money($item->price )}}</td>
                                            <td class="text-center px-2">{{ $item->quantity }}</td>
                                            <td class="text-right px-2" width="15%">{{ money($item->total) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="px-3" colspan="5">Tidak ada produk</td>
                                        </tr>
                                    @endforelse
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-center"><strong>Total</strong></td>
                                        <td class="text-center"><b>{{ angka($returnGood->retur_items->sum('quantity')) }}</b></td>
                                        <td class="text-right px-2"><b>{{ money($returnGood->nominal) }}</b></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @endforeach

                <div class="border-top mt-2 pt-2 text-right ml-5">
                    <p class="m-0">Total Faktur Retur</p>
                    <h5 class="m-0"><strong>{{ money($returs->sum('nominal')) }}</strong></h5>
                </div>
            </section>

            <div class="row text-right my-4">
                <div class="col text-left">
                    <h6 class="m-0">Total Tagihan</h6>
                </div>

                @php
                    $faktur = $invoices->sum('total');
                    $diskon = $invoices->sum('discount');
                    $adjustment = $adjustments->sum('amount');
                    $retur = $returs->sum('nominal');
                    $tagihan = $faktur - ($adjustment + $diskon + $retur);
                @endphp

                <div class="col-auto">
                    <p class="mb-0">
                        <span>Total Tagihan</span>
                        <br />
                        <span class="h5 mb-0 tagihan-total"><strong>{{ money($tagihan) }}</strong></span>
                    </p>
                </div>
            </div>

            {{--Pembayaran --}}
            <section class="border-top py-3 mt-5" id="modelBayar">
                <div class="row mb-2">
                    <div class="col">
                        <h6>Pembayaran</h6>

                        <p class="mb-0">Total pembayaran: {{ $payments->count() }}</p>
                    </div>

                    <div class="col-auto">
                        <a href="{{ route('admin.payments.create') }}" class="btn btn-sm btn-success">Tambah Pembayaran</a>
                    </div>
                </div>

                <table class="table table-bordered table-hover m-0">
                    <thead>
                        <tr>
                            <th class="text-center" width="1%">No.</th>
                            <th>No. Kwitansi</th>
                            <th class="text-center px-3" width="100">Tanggal</th>
                            <th class="text-center px-3">Semester</th>
                            <th class="text-center px-3">Metode Pembayaran</th>
                            <th class="text-center px-3" width="20%">Bayar</th>
                            <th class="text-center px-3" width="15%">Diskon</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($payments as $pembayaran)
                            <tr>
                                <td class="text-right px-3">{{ $loop->iteration }}.</td>
                                <td>
                                    <div class="row text-center">
                                        <div class="col">
                                            <span>{{ $pembayaran->no_kwitansi }}</span>
                                            <a href="{{ route('admin.payments.kwitansi', $pembayaran->id) }}" title="Cetak Pembayaran" target="_blank" class="text-info ml-1">
                                                <i class="fa fa-print"></i>
                                            </a>
                                        </div>

                                        <div class="col-auto">
                                            <a href="{{ route('admin.payments.edit', $pembayaran->id) }}" title="Edit Pembayaran" class="text-info ml-1">
                                                <i class="fa fa-edit"></i> Edit
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center px-2">{{ $pembayaran->date }}</td>
                                <td class="text-center">{{ $pembayaran->semester->name }}</td>
                                <td class="text-center">{{ App\Models\Payment::PAYMENT_METHOD_SELECT[$pembayaran->payment_method] }}</td>
                                <td class="text-right px-3">{{ money($pembayaran->paid) }}</td>
                                <td class="text-right px-3">
                                    <span>{{ money($pembayaran->discount) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-3" colspan="6">Belum ada pembayaran</td>
                            </tr>
                        @endforelse
                    </tbody>

                    <tfoot>
                        <tr>
                            <td class="text-center px-3" colspan="4">
                                <strong>Total</strong>
                            </td>
                            <td class="text-right px-3">
                                <strong>{{ money($payments->sum('paid')) }}</strong>
                            </td>
                            <td class="text-right px-3">
                                <strong>{{ money($payments->sum('discount')) }}</strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>

                <hr class="my-2 text-right ml-5 mx-0" />

                <div class="row text-right">
                    <div class="col text-left">

                    </div>

                    <div class="col-auto">
                        <p class="mb-0">
                            <span>Total Pembayaran</span>
                            <br />
                            <span class="h5 mb-0 pembayaran-total"><strong>{{ money($payments->sum('paid')) }}</strong></span>
                        </p>
                    </div>

                    <div class="col-auto">
                        <p class="mb-0">
                            <span>Total Potongan</span>
                            <br />
                            <span class="h5 mb-0 pembayaran-diskon"><strong>{{ money($payments->sum('discount')) }}</strong></span>
                        </p>
                    </div>
                </div>
            </section>

            {{--Resume --}}
            <section class="border-top py-3 mt-5" id="modelResume">
                @php
                    $total_faktur = $invoices->sum('total');
                    $total_diskon = $invoices->sum('discount');
                    $total_retur = $returs->sum('nominal');
                    $total_bayar = $payments->sum('paid');
                    $total_potongan = $payments->sum('discount');
                    $saldo_sebelumnya = $bills->sum('saldo_akhir');
                    $total_utang = $saldo_sebelumnya + $total_faktur - ($total_diskon + $total_retur + $total_bayar + $total_potongan);
                @endphp
                <div class="row mb-2">
                    <div class="col">
                        <h6>Resume</h6>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered table-hover m-0">
                            <thead>
                                <tr>
                                    <th class="text-center" width="1%">No.</th>
                                    <th class="text-center">Total Of</th>
                                    <th class="text-center" width="35%">Amount</th>
                                </tr>
                            </thead>

                            <tbody>
                                @if($saldo_sebelumnya)
                                <tr>
                                    <td class="text-right px-3">1.</td>
                                    <td class="text-center">Saldo Hutang</td>
                                    <td class="text-right px-3"><strong>{{ money($saldo_sebelumnya)}}</strong></td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="text-right px-3">2.</td>
                                    <td class="text-center">Faktur Penjualan</td>
                                    <td class="text-right px-3"><strong>{{ money($total_faktur)}}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-right px-3">3.</td>
                                    <td class="text-center">Diskon</td>
                                    <td class="text-right px-3"><strong>{{ money($total_diskon)}}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-right px-3">4.</td>
                                    <td class="text-center">Faktur Retur</td>
                                    <td class="text-right px-3"><strong>{{ money($total_retur)}}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-right px-3">5.</td>
                                    <td class="text-center">Pembayaran</td>
                                    <td class="text-right px-3"><strong>{{ money($total_bayar)}}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-right px-3">6.</td>
                                    <td class="text-center">Potongan</td>
                                    <td class="text-right px-3"><strong>{{ money($total_potongan)}}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-right px-3">7.</td>
                                    <td class="text-center">Sisa Hutang</td>
                                    <td class="text-right px-3"><strong>{{ money($total_utang)}}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="border-top mt-2 pt-2 text-right ml-5">
                    <p class="m-0">Sisa Hutang</p>
                    <h5 class="m-0"><strong>{{ money($total_utang) }}</strong></h5>
                </div>
            </section>
            <br>
            @foreach ($bills as $bill)
                {{--Semester --}}
                <section class="border-top py-3 mt-5" id="modelSemester{{ $bill->semester_id }}">
                    @php
                        $fakturs = $invoices_old->where('semester_id', $bill->semester_id);
                        $returs = $returs_old->where('semester_id', $bill->semester_id);
                        $payments = $payments_old->where('semester_id', $bill->semester_id);
                    @endphp

                    <div class="row mb-2">
                        <div class="col">
                            <h6>{{ $bill->semester->name }}</h6>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body px-3 py-2">
                            <div class="row">
                                <div class="col-6 mb-1">
                                    <span class="badge badge-success">Faktur Penjualan</span>
                                </div>


                                <div class="col-3">
                                    <p class="mb-0 text-sm">
                                        Salesman
                                        <br />
                                        <strong>{{ $bill->salesperson->name }} - {{ $bill->salesperson->marketing_area->name }} </strong>
                                    </p>
                                </div>

                                <div class="col-3">
                                    <p class="mb-0 text-sm">
                                        Semester
                                        <br />
                                        <strong>{{ $bill->semester->name }}</strong>
                                    </p>
                                </div>

                            </div>

                            <p class="mt-4 mb-1">
                                <strong>Daftar Faktur Penjualan</strong>
                            </p>

                            <table class="table table-sm table-bordered m-0">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="1%">No.</th>
                                        <th>No Faktur</th>
                                        <th class="px-2">Date</th>
                                        <th class="px-2">Total</th>
                                        <th class="px-2">Diskon</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($fakturs as $item)
                                        <tr>
                                            <td class="text-right px-3">{{ $loop->iteration }}.</td>
                                            <td class="text-center px-2">
                                                {{ $item->no_faktur }}
                                                <a href="{{ route('admin.invoices.print-faktur', $item->id) }}" title="Cetak Faktur" target="_blank" class="text-info ml-1">
                                                    <i class="fa fa-print"></i>
                                                </a>
                                            </td>
                                            <td class="text-center px-2">{{ $item->date }}</td>
                                            <td class="text-right px-2">{{ money($item->total) }}</td>
                                            <td class="text-right px-2">{{ money($item->discount) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="px-3" colspan="5">Tidak ada Faktur Penjualan</td>
                                        </tr>
                                    @endforelse
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <td colspan="3">Total</td>
                                        <td class="text-right px-2"><strong>{{ money($fakturs->sum('total')) }}</strong></td>
                                        <td class="text-right px-2"><strong>{{ money($fakturs->sum('discount')) }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body px-3 py-2">
                            <div class="row">
                                <div class="col-6 mb-1">
                                    <span class="badge badge-danger">Faktur Retur</span>
                                </div>


                                <div class="col-3">
                                    <p class="mb-0 text-sm">
                                        Salesman
                                        <br />
                                        <strong>{{ $bill->salesperson->name }} - {{ $bill->salesperson->marketing_area->name }} </strong>
                                    </p>
                                </div>

                                <div class="col-3">
                                    <p class="mb-0 text-sm">
                                        Semester
                                        <br />
                                        <strong>{{ $bill->semester->name }}</strong>
                                    </p>
                                </div>

                            </div>

                            <p class="mt-4 mb-1">
                                <strong>Daftar Faktur Retur</strong>
                            </p>

                            <table class="table table-sm table-bordered m-0">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="1%">No.</th>
                                        <th>No Faktur Retur</th>
                                        <th class="px-2">Date</th>
                                        <th class="px-2">Total</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($returs as $item)
                                        <tr>
                                            <td class="text-right px-3">{{ $loop->iteration }}.</td>
                                            <td class="text-center px-2">
                                                {{ $item->no_retur }}
                                                <a href="{{ route('admin.return-goods.print-faktur', $item->id) }}" title="Cetak Faktur" target="_blank" class="text-info ml-1">
                                                    <i class="fa fa-print"></i>
                                                </a>
                                            </td>
                                            <td class="text-center px-2">{{ $item->date }}</td>
                                            <td class="text-right px-2">{{ money($item->nominal) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="px-3" colspan="5">Tidak ada Faktur Retur</td>
                                        </tr>
                                    @endforelse
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <td colspan="3">Total</td>
                                        <td class="text-right px-2"><strong>{{ money($returs->sum('nominal')) }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body px-3 py-2">
                            <div class="row">
                                <div class="col-6 mb-1">
                                    <span class="badge badge-warning">Pembayaran</span>
                                </div>


                                <div class="col-3">
                                    <p class="mb-0 text-sm">
                                        Salesman
                                        <br />
                                        <strong>{{ $bill->salesperson->name }} - {{ $bill->salesperson->marketing_area->name }} </strong>
                                    </p>
                                </div>

                                <div class="col-3">
                                    <p class="mb-0 text-sm">
                                        Semester
                                        <br />
                                        <strong>{{ $bill->semester->name }}</strong>
                                    </p>
                                </div>

                            </div>

                            <p class="mt-4 mb-1">
                                <strong>Daftar Pembayaran</strong>
                            </p>

                            <table class="table table-sm table-bordered m-0">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="1%">No.</th>
                                        <th class="text-center">No Kwitansi</th>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">Semester</th>
                                        <th class="text-center">Bayar</th>
                                        <th class="text-center">Potongan</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($payments as $item)
                                        <tr>
                                            <td class="text-right px-3">{{ $loop->iteration }}.</td>
                                            <td class="text-center px-2">
                                                {{ $item->no_kwitansi }}
                                                <a href="{{ route('admin.payments.kwitansi', $item->id) }}" title="Cetak Pembayaran" target="_blank" class="text-info ml-1">
                                                    <i class="fa fa-print"></i>
                                                </a>
                                            </td>
                                            <td class="text-center">{{ $item->date }}</td>
                                            <td class="text-center">{{ $item->semester->name }}</td>
                                            <td class="text-right px-2">{{ money($item->paid) }}</td>
                                            <td class="text-right px-2">{{ money($item->discount) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="px-3" colspan="5">Tidak ada Pembayaran</td>
                                        </tr>
                                    @endforelse
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <td colspan="3">Total</td>
                                        <td class="text-right px-2"><strong>{{ money($payments->sum('paid')) }}</strong></td>
                                        <td class="text-right px-2"><strong>{{ money($payments->sum('discount')) }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </section>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function($) {
    $(function() {
        var maxScroll = $(document).height() - $(window).height();
        var detail = $('.model-detail');
        var nav = $('.breadcrumb-nav');
        var navHi = nav.height();
        var sections = detail.children('section');
        var tops = sections.map(function (index, item) {
            return $(item).offset().top;
        });

        $(window).on('scroll', function(e) {
            var scroll = e.currentTarget.scrollY + navHi;
            var section;
            console.log(section);

            tops.map(function(index, item) {
                if (scroll >= item) {
                    section = sections.eq(index);
                }
            });

            if (scroll >= maxScroll) {
                section = sections.eq(tops.length - 1);
            }

            if (section) {
                var id = section.attr('id');
                var navLink = nav.find('a[href="#'+id+'"]');

                nav.find('a').removeClass('active');
                navLink.length && navLink.addClass('active');
            }
        });

        nav.find('a').on('click', function(e) {
            e.preventDefault();

            var el = $(e.currentTarget);
            var href = el.attr('href');
            var target = $(href);

            target.length && $('html, body').animate({
                scrollTop: target.offset().top - nav.height()
            }, 500, 'linear');
        });
    });
})(jQuery);
</script>
@endpush
