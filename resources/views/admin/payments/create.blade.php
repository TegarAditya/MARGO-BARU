@extends('layouts.admin')
@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1 class="m-0">Formulir Pembayaran Sales</h1>
    </div>
</div>
<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.payment.title_singular') }}
    </div>

    <div class="card-body">
        @if (session()->has('error-message'))
            <p class="text-danger">
                {{session()->get('error-message')}}
            </p>
        @endif

        <form class="form-prevent-multiple-submits" method="POST" id="paymentForm" action="{{ route("admin.payments.store") }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="bayar" value="0" />
            <input type="hidden" name="diskon" value="0" />
            <input type="hidden" name="nominal" value="0" />

            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="no_kwitansi">{{ trans('cruds.payment.fields.no_kwitansi') }}</label>
                        <input class="form-control {{ $errors->has('no_kwitansi') ? 'is-invalid' : '' }}" type="text" name="no_kwitansi" id="no_kwitansi" value="{{ old('no_kwitansi', $no_kwitansi) }}" placeholder="(Otomatis)" readonly>
                        @if($errors->has('no_kwitansi'))
                            <span class="text-danger">{{ $errors->first('no_kwitansi') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.payment.fields.no_kwitansi_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="date">{{ trans('cruds.payment.fields.date') }}</label>
                        <input class="form-control date {{ $errors->has('date') ? 'is-invalid' : '' }}" type="text" name="date" id="date" value="{{ old('date', $today) }}" required>
                        @if($errors->has('date'))
                            <span class="text-danger">{{ $errors->first('date') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.payment.fields.date_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required">{{ trans('cruds.payment.fields.payment_method') }}</label>
                        <select class="form-control {{ $errors->has('payment_method') ? 'is-invalid' : '' }}" name="payment_method" id="payment_method" required>
                            <option value disabled {{ old('payment_method', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                            @foreach(App\Models\Payment::PAYMENT_METHOD_SELECT as $key => $label)
                                <option value="{{ $key }}" {{ old('payment_method', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('payment_method'))
                            <span class="text-danger">{{ $errors->first('payment_method') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.payment.fields.payment_method_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="salesperson_id">{{ trans('cruds.payment.fields.salesperson') }}</label>
                        <select class="form-control select2 {{ $errors->has('salesperson') ? 'is-invalid' : '' }}" name="salesperson_id" id="salesperson_id" required>
                            @foreach($salespeople as $id => $entry)
                                <option value="{{ $id }}" {{ old('salesperson_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('salesperson'))
                            <span class="text-danger">{{ $errors->first('salesperson') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.payment.fields.salesperson_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="semester_id">{{ trans('cruds.payment.fields.semester') }}</label>
                        <select class="form-control select2 {{ $errors->has('semester') ? 'is-invalid' : '' }}" name="semester_id" id="semester_id" required>
                            @foreach($semesters as $id => $entry)
                                <option value="{{ $id }}" {{ old('semester_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('semester'))
                            <span class="text-danger">{{ $errors->first('semester') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.payment.fields.semester_helper') }}</span>
                    </div>
                </div>
                <div class="col-12">
                    <div id="tagihan">
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label class="required" for="bayar">Bayar</label>
                        <div class="form-group text-field m-0">
                            <div class="text-field-input px-2 py-0">
                                <span class="mr-1">Rp</span>
                                <input class="form-control" type="text" id="bayar_text" name="bayar_text" min="1">
                                <label for="bayar_text" class="text-field-border"></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <p class="font-weight-bold mb-1">Diskon</p>
                    <div class="row mb-3">
                        <div class="col-auto">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="diskon_type" id="diskon_type-1" value="" data-prefix="" checked>
                                <label class="form-check-label" for="diskon_type-1">
                                    Tidak Ada
                                </label>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="diskon_type" id="diskon_type-2" value="percent" data-prefix="%">
                                <label class="form-check-label" for="diskon_type-2">
                                    Persen (%)
                                </label>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="diskon_type" id="diskon_type-3" value="value" data-prefix="Rp">
                                <label class="form-check-label" for="diskon_type-3">
                                    Nominal (Rp)
                                </label>
                            </div>
                        </div>

                        <div class="col-12 mt-2 diskon-nominal" style="display:none">
                            <p class="mb-0 text-sm">Nominal Diskon</p>
                            <div class="form-group text-field m-0">
                                <div class="text-field-input px-2 py-0">
                                    <span class="text-sm mr-1 diskon-prefix"></span>
                                    <input class="form-control" type="number" id="diskon_amount" name="diskon_amount" value="0" min="1">
                                    <label for="diskon_amount" class="text-field-border"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label class="required" for="nominal">Nominal Bayar</label>
                        <div class="form-group text-field m-0">
                            <div class="text-field-input px-2 py-0">
                                <input class="form-control" type="text" id="nominal" name="nominal_text" value="0" min="1" readonly="readonly">
                                <label for="nominal" class="text-field-border"></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label for="note">{{ trans('cruds.payment.fields.note') }}</label>
                        <textarea class="form-control {{ $errors->has('note') ? 'is-invalid' : '' }}" name="note" id="note">{{ old('note') }}</textarea>
                        @if($errors->has('note'))
                            <span class="text-danger">{{ $errors->first('note') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.payment.fields.note_helper') }}</span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <button class="btn btn-danger form-prevent-multiple-submits" type="submit">
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
    var diskonTypes = form.find('[name="diskon_type"]');
    var diskonAmount = form.find('[name="diskon_amount"]');
    var diskon = form.find('[name="diskon"]');
    var bayar = form.find('[name="bayar"]');
    var bayarText = form.find('[name="bayar_text"]');

    bayarText.on('change keyup blur paste', function(e) {
        var value = numeral(e.target.value);
        bayarText.val(value.format('0,0'));
        bayar.val(value.value()).trigger('change');
    }).trigger('change');

    diskonTypes.on('change', function(e) {
        var el = $(e.currentTarget);
        var prefix = el.data('prefix');
        var value = el.val();
        var bayarVal = parseFloat(bayar.val()) || 0;
        var diskonVal = parseFloat(diskonAmount.val()) || 0;

        $('.diskon-prefix').html(prefix || '');
        $('.diskon-nominal')[!value ? 'hide' : 'show']();
        diskonAmount.attr('min', !value ? null : 1);

        if ('percent' === value && diskonVal > 100) {
            diskonAmount.val(Math.round(diskonVal * 100 / bayarVal));
        } else if ('value' === value && diskonVal <= 100) {
            diskonAmount.val(Math.round(bayarVal * diskonVal / 100));
        }

        diskonAmount.trigger('change');
    }).filter(':checked').trigger('change');

    diskonAmount.on('change keyup blur', function(e) {
        var value = parseFloat(diskonAmount.val()) || 0;
        var isPercent = 'percent' === diskonTypes.filter(':checked').val();

        (isPercent && value) > 100 && diskonAmount.val(100);
    });

    bayar.add(diskonAmount).on('change keyup blur', function(e) {
        var max = Math.abs(bayar.attr('max'));
        var bayarVal = parseFloat(bayar.val()) || 0;
        var diskonVal = parseFloat(diskonAmount.val()) || 0;
        var diskonType = diskonTypes.filter(':checked').val();
        var diskonCalc = diskonType !== 'percent' ? (
            diskonType !== 'value' ? 0 : diskonVal
        ) : ((diskonVal / 100)  * bayarVal);

        bayarVal = (max && max < bayarVal) ? max : bayarVal;

        var diskonRp = diskonCalc <= bayarVal ? diskonCalc : bayarVal;
        diskonVal = diskonCalc <= bayarVal ? diskonVal : (
            diskonType === 'percent' ? 100 : bayarVal
        );

        var value = Math.round(bayarVal + diskonRp);

        bayar.val(bayarVal);
        nominal.val(value);
        nominalText.val(numeral(value).format('$0,0'));
        diskon.val(diskonRp);
        diskonAmount.val(diskonVal);
    });

    $('#salesperson_id').on('select2:select', function (e) {
        var salesperson = e.params.data.id;

        $.ajax({
            type: "GET",
            url: "{{ route('admin.payments.getTagihan') }}",
            data: {
                salesperson: salesperson
            },
            success: function (response) {
                $('#tagihan').html('');
                console.log(response);
                if (response.status == 'error') {
                    var formHtml = `
                        <div class="detail-tagihan mt-3 mb-4">
                            <p class="mb-0 font-weight-bold">Tidak Ada Tagihan</p>
                        </div>
                    `;
                    $('#tagihan').prepend(formHtml);
                }

                if (response.status == 'success') {
                    response.bills.forEach(bill => {
                        var formHtml = `
                            <div class="detail-tagihan mt-3 mb-4">
                                <p class="mb-0 font-weight-bold">Detail Tagihan ${bill.semester.name}</p>
                                <div class="row">
                                    <div class="col-auto" style="min-width: 160px">
                                        <p class="mb-0">
                                            <small class="font-weight-bold">Saldo Awal</small>
                                            <br />
                                            <span class="tagihan-total">${convertToRupiah(parseInt(bill.saldo_awal))}</span>
                                        </p>
                                    </div>
                                    <div class="col-auto" style="min-width: 160px">
                                        <p class="mb-0">
                                            <small class="font-weight-bold">Total Tagihan</small>
                                            <br />
                                            <span class="tagihan-total">${convertToRupiah(parseInt(bill.jual) - (parseInt(bill.diskon) + parseInt(bill.retur)))}</span>
                                        </p>
                                    </div>

                                    <div class="col-auto"  style="min-width: 160px">
                                        <p class="mb-0">
                                            <small class="font-weight-bold">Total Pembayaran</small>
                                            <br />
                                            <span class="tagihan-saldo">${convertToRupiah(parseInt(bill.bayar) + parseInt(bill.potongan))}</span>
                                        </p>
                                    </div>

                                    <div class="col-auto"  style="min-width: 160px">
                                        <p class="mb-0">
                                            <small class="font-weight-bold">Sisa Tagihan</small>
                                            <br />
                                            <span class="tagihan-sisa">${convertToRupiah(parseInt(bill.saldo_akhir))}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        `;
                        $('#tagihan').prepend(formHtml);
                    });
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
