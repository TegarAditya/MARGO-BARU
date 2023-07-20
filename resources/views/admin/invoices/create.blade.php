@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.invoice.title_singular') }}
    </div>

    <div class="card-body">
        @if (session()->has('error-message'))
            <p class="text-danger">
                {{session()->get('error-message')}}
            </p>
        @endif

        <form id="invoiceForm" method="POST" action="{{ route("admin.invoices.storeInvoice") }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="no_faktur">{{ trans('cruds.invoice.fields.no_faktur') }}</label>
                        <input class="form-control {{ $errors->has('no_faktur') ? 'is-invalid' : '' }}" type="text" name="no_faktur" id="no_faktur" value="{{ old('no_faktur', '') }}" readonly placeholder="(Otomatis)">
                        @if($errors->has('no_faktur'))
                            <span class="text-danger">{{ $errors->first('no_faktur') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.invoice.fields.no_faktur_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="date">{{ trans('cruds.invoice.fields.date') }}</label>
                        <input class="form-control date {{ $errors->has('date') ? 'is-invalid' : '' }}" type="text" name="date" id="date" value="{{ old('date') }}" required>
                        @if($errors->has('date'))
                            <span class="text-danger">{{ $errors->first('date') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.invoice.fields.date_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="salesperson_id">{{ trans('cruds.invoice.fields.salesperson') }}</label>
                        <select class="form-control select2 {{ $errors->has('salesperson') ? 'is-invalid' : '' }}" name="salesperson_id" id="salesperson_id" required>
                            @foreach($salespeople as $id => $entry)
                                <option value="{{ $id }}" {{ old('salesperson_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('salesperson'))
                            <span class="text-danger">{{ $errors->first('salesperson') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.invoice.fields.salesperson_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="semester_id">{{ trans('cruds.invoice.fields.semester') }}</label>
                        <select class="form-control select2 {{ $errors->has('semester') ? 'is-invalid' : '' }}" name="semester_id" id="semester_id" required>
                            @foreach($semesters as $id => $entry)
                                <option value="{{ $id }}" {{ old('semester_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('semester'))
                            <span class="text-danger">{{ $errors->first('semester') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.invoice.fields.semester_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="delivery_order_id">{{ trans('cruds.invoice.fields.delivery_order') }}</label>
                        <select class="form-control select2 {{ $errors->has('delivery_order') ? 'is-invalid' : '' }}" name="delivery_order_id" id="delivery_order_id">
                            <option></option>
                        </select>
                        @if($errors->has('delivery_order'))
                            <span class="text-danger">{{ $errors->first('delivery_order') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.invoice.fields.delivery_order_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required">{{ trans('cruds.invoice.fields.type') }}</label>
                        <select class="form-control select2 {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type" id="type" required>
                            <option value disabled {{ old('type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                            @foreach(App\Models\Invoice::TYPE_SELECT as $key => $label)
                                <option value="{{ $key }}" {{ old('type', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('type'))
                            <span class="text-danger">{{ $errors->first('type') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.invoice.fields.type_helper') }}</span>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label for="note" class="required">{{ trans('cruds.invoice.fields.note') }}</label>
                        <textarea class="form-control {{ $errors->has('note') ? 'is-invalid' : '' }}" name="note" id="note" required>{{ old('note') }}</textarea>
                        @if($errors->has('note'))
                            <span class="text-danger">{{ $errors->first('note') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.invoice.fields.note_helper') }}</span>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label class="required" for="bayar">Nominal</label>
                        <div class="form-group text-field m-0">
                            <div class="text-field-input px-2 py-0">
                                <span class="mr-1">Rp</span>
                                <input type="hidden" name="nominal" value="1" />
                                <input class="form-control" type="text" id="nominal_text" name="nominal_text" min="1">
                                <label for="nominal_text" class="text-field-border"></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col"></div>

                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Simpan Faktur</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function($, numeral) {
    $(function() {
        $('#delivery_order_id').select2({
            ajax: {
                url: "{{ route('admin.delivery-orders.getDeliveryOrder') }}",
                data: function() {
                    return {
                        semester: $('#semester_id').val(),
                        salesperson: $('#salesperson_id').val()
                    };
                },
                dataType: 'json',
                processResults: function(data) {
                    return {
                        results: data
                    };
                }
            }
        });

        var form = $('#invoiceForm');
        var nominal = form.find('[name="nominal"]');
        var nominalText = form.find('[name="nominal_text"]');

        nominalText.on('change keyup blur paste', function(e) {
            var value = numeral(e.target.value);

            nominalText.val(value.format('0,0'));
            nominal.val(value.value()).trigger('change');
        }).trigger('change');

    });
})(jQuery, window.numeral);
</script>
@endsection
