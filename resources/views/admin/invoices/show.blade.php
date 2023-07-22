@extends('layouts.admin')
@section('content')

<div class="row mb-4">
    <div class="col-12">
        <h1 class="m-0">Invoice / Faktur</h1>
    </div>
</div>
<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.invoice.title') }}
    </div>

    <div class="card-body">
        <div class="model-detail">
            <section class="py-3">
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

                            <div class="col-3">
                                <p class="mb-0 text-sm">
                                    No. Invoice
                                    <br />
                                    <strong>{{ $invoice->no_faktur }}</strong>

                                    <a href="{{ route('admin.invoices.print-faktur', $invoice->id) }}" class="fa fa-print ml-1 text-info" title="Print Invoice" target="_blank"></a>
                                </p>
                            </div>

                            <div class="col text-right">
                                <span>Tanggal<br />{{ $invoice->date }}</span>
                            </div>
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
                                    <th>Tema/Mapel</th>
                                    <th class="px-2">Harga</th>
                                    <th class="px-2">Quantity</th>
                                    <th class="px-2">Total</th>
                                    <th class="px-2">Diskon</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($invoice_items as $item)
                                    @php
                                        $product = $item->product;
                                    @endphp
                                    <tr>
                                        <td class="text-right px-3">{{ $loop->iteration }}.</td>
                                        {{-- <td class="text-center">{{ $product->jenjang->name ?? '' }} - {{ $product->kurikulum->code ?? '' }}</td> --}}
                                        <td>{{ $product->name }}</td>
                                        <td class="text-right px-2">{{ money($item->price )}}</td>
                                        <td class="text-center px-2">{{ $item->quantity }}</td>
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
                                    <td colspan="9"><br></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-right px-5"><strong>Subtotal</strong></td>
                                    <td colspan="4" class="text-right px-5"><b>{{ money($invoice->total) }}</b></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-right px-5"><strong>Discount</strong></td>
                                    <td colspan="4" class="text-right px-5"><b>{{ money($invoice->discount) }}</b></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-right px-5"><strong>Grand Total</strong></td>
                                    <td colspan="4" class="text-right px-5"><b>{{ money($invoice->nominal) }}</b></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </section>
        </div>
        <div class="row mt-3">

            <div class="col">
                <a class="btn btn-primary" href="{{ url()->previous() }}">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
            </div>

            <div class="col-auto">

            </div>
        </div>
    </div>
</div>



@endsection
