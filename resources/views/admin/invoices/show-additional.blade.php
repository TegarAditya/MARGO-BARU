@extends('layouts.admin')
@section('content')

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
                                    <th class="text-center">Keperluan</th>
                                    <th>Catatan</th>
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
