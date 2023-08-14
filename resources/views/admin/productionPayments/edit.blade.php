@extends('layouts.admin')
@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1 class="m-0">Pembayaran Vendor</h1>
    </div>
</div>
<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.productionPayment.title_singular') }}
    </div>

    <div class="card-body">
        <form id="paymentForm" method="POST" action="{{ route("admin.production-payments.update", [$productionPayment->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="no_payment">{{ trans('cruds.productionPayment.fields.no_payment') }}</label>
                        <input class="form-control {{ $errors->has('no_payment') ? 'is-invalid' : '' }}" type="text" name="no_payment" id="no_payment" value="{{ old('no_payment', $productionPayment->no_payment) }}" readonly>
                        @if($errors->has('no_payment'))
                            <span class="text-danger">{{ $errors->first('no_payment') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.productionPayment.fields.no_payment_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="date">{{ trans('cruds.productionPayment.fields.date') }}</label>
                        <input class="form-control date {{ $errors->has('date') ? 'is-invalid' : '' }}" type="text" name="date" id="date" value="{{ old('date', $productionPayment->date) }}" required>
                        @if($errors->has('date'))
                            <span class="text-danger">{{ $errors->first('date') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.productionPayment.fields.date_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label>{{ trans('cruds.productionPayment.fields.payment_method') }}</label>
                        <select class="form-control {{ $errors->has('payment_method') ? 'is-invalid' : '' }}" name="payment_method" id="payment_method">
                            <option value disabled {{ old('payment_method', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                            @foreach(App\Models\ProductionPayment::PAYMENT_METHOD_SELECT as $key => $label)
                                <option value="{{ $key }}" {{ old('payment_method', $productionPayment->payment_method) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('payment_method'))
                            <span class="text-danger">{{ $errors->first('payment_method') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.productionPayment.fields.payment_method_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="vendor_id">{{ trans('cruds.productionPayment.fields.vendor') }}</label>
                        <select class="form-control select2 {{ $errors->has('vendor') ? 'is-invalid' : '' }}" name="vendor_id" id="vendor_id" required>
                            @foreach($vendors as $id => $entry)
                                <option value="{{ $id }}" {{ (old('vendor_id') ? old('vendor_id') : $productionPayment->vendor->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('vendor'))
                            <span class="text-danger">{{ $errors->first('vendor') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.productionPayment.fields.vendor_helper') }}</span>
                    </div>
                </div>
                <div class="col-12">
                    <div id="tagihan">
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label class="required" for="bayar">Nominal</label>
                        <div class="form-group text-field m-0">
                            <div class="text-field-input px-2 py-0">
                                <span class="mr-1">Rp</span>
                                <input type="hidden" name="nominal" value="{{ $productionPayment->nominal }}" />
                                <input class="form-control" type="text" id="nominal_text" name="nominal_text" value="{{ angka($productionPayment->nominal) }}">
                                <label for="nominal_text" class="text-field-border"></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label for="note">{{ trans('cruds.productionPayment.fields.note') }}</label>
                        <textarea class="form-control {{ $errors->has('note') ? 'is-invalid' : '' }}" name="note" id="note">{{ old('note', $productionPayment->note) }}</textarea>
                        @if($errors->has('note'))
                            <span class="text-danger">{{ $errors->first('note') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.productionPayment.fields.note_helper') }}</span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <button class="btn btn-danger" type="submit">
                            Submit
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
@section('scripts')
<script>
$(document).ready(function() {
    var form = $('#paymentForm');
    var nominal = form.find('[name="nominal"]');
    var nominalText = form.find('[name="nominal_text"]');

    nominalText.on('change keyup blur paste', function(e) {
        var value = numeral(e.target.value);
        nominalText.val(value.format('0,0'));
        nominal.val(value.value()).trigger('change');
    }).trigger('change');

    $('#vendor_id').on('select2:select', function (e) {
        var vendor = e.params.data.id;

        $.ajax({
            type: "GET",
            url: "{{ route('admin.production-payments.getTagihan') }}",
            data: {
                vendor: vendor
            },
            success: function (response) {
                $('#tagihan').html('');
                console.log(response);
                if (response.status == 'error') {
                    var formHtml = `
                        <div class="detail-tagihan mt-3 mb-4">
                            <p class="mb-0 font-weight-bold">Tidak Ada Ongkos</p>
                        </div>
                    `;
                    $('#tagihan').prepend(formHtml);
                }

                if (response.status == 'success') {
                    var formHtml = `
                        <div class="detail-tagihan mt-3 mb-4">
                            <p class="mb-0 font-weight-bold">Detail Ongkos</p>
                            <div class="row">
                                <div class="col-auto"  style="min-width: 160px">
                                    <p class="mb-0">
                                        <small class="font-weight-bold">Sisa Tagihan</small>
                                        <br />
                                        <span class="tagihan-sisa">${convertToRupiah(parseInt(response.tagihan.outstanding_fee))}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    `;
                    $('#tagihan').prepend(formHtml);
                }
            }
        });
    });

    function convertToRupiah(angka)
    {
        var rupiah = '';
        var angkarev = angka.toString().split('').reverse().join('');
        for(var i = 0; i < angkarev.length; i++) if(i%3 == 0) rupiah += angkarev.substr(i,3)+'.';
        return 'Rp. '+rupiah.split('',rupiah.length-1).reverse().join('');
    }
});
</script>
@endsection