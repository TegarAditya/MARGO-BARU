@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.payment.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.payments.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.payment.fields.no_kwitansi') }}
                        </th>
                        <td>
                            {{ $payment->no_kwitansi }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.payment.fields.date') }}
                        </th>
                        <td>
                            {{ $payment->date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.payment.fields.semester') }}
                        </th>
                        <td>
                            {{ $payment->semester->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.payment.fields.salesperson') }}
                        </th>
                        <td>
                            {{ $payment->salesperson->short_name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.payment.fields.payment_method') }}
                        </th>
                        <td>
                            {{ App\Models\Payment::PAYMENT_METHOD_SELECT[$payment->payment_method] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Bayar
                        </th>
                        <td>
                            {{ money($payment->paid) }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Potongan
                        </th>
                        <td>
                            {{ money($payment->discount) }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Nominal
                        </th>
                        <td>
                            {{ money($payment->amount) }}
                        </td>
                    </tr>
                    
                    <tr>
                        <th>
                            {{ trans('cruds.payment.fields.note') }}
                        </th>
                        <td>
                            {{ $payment->note }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.payments.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection