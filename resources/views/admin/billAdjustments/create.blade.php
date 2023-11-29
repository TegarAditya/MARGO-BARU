@extends('layouts.admin')
@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1 class="m-0 bold">Billing Adjustment</h1>
    </div>
</div>

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.billAdjustment.title_singular') }}
    </div>

    <div class="card-body">
        @if (session()->has('error-message'))
            <p class="text-danger">
                {{session()->get('error-message')}}
            </p>
        @endif
        
        <form class="form-prevent-multiple-submits" id="paymentForm" method="POST" action="{{ route("admin.bill-adjustments.store") }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="amount" value="0" />
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="no_adjustment">{{ trans('cruds.billAdjustment.fields.no_adjustment') }}</label>
                        <input class="form-control {{ $errors->has('no_adjustment') ? 'is-invalid' : '' }}" type="text" name="no_adjustment" id="no_adjustment" value="{{ old('no_adjustment', $no_adjustment) }}" required readonly>
                        @if($errors->has('no_adjustment'))
                            <span class="text-danger">{{ $errors->first('no_adjustment') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.billAdjustment.fields.no_adjustment_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="date">{{ trans('cruds.billAdjustment.fields.date') }}</label>
                        <input class="form-control date {{ $errors->has('date') ? 'is-invalid' : '' }}" type="text" name="date" id="date" value="{{ old('date', $today) }}" required>
                        @if($errors->has('date'))
                            <span class="text-danger">{{ $errors->first('date') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.billAdjustment.fields.date_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="salesperson_id">{{ trans('cruds.billAdjustment.fields.salesperson') }}</label>
                        <select class="form-control select2 {{ $errors->has('salesperson') ? 'is-invalid' : '' }}" name="salesperson_id" id="salesperson_id" required>
                            @foreach($salespeople as $id => $entry)
                                <option value="{{ $id }}" {{ old('salesperson_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('salesperson'))
                            <span class="text-danger">{{ $errors->first('salesperson') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.billAdjustment.fields.salesperson_helper') }}</span>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label class="required" for="bayar">{{ trans('cruds.billAdjustment.fields.amount') }}</label>
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
                    <div class="form-group">
                        <label for="note">{{ trans('cruds.billAdjustment.fields.note') }}</label>
                        <textarea class="form-control {{ $errors->has('note') ? 'is-invalid' : '' }}" name="note" id="note">{{ old('note') }}</textarea>
                        @if($errors->has('note'))
                            <span class="text-danger">{{ $errors->first('note') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.billAdjustment.fields.note_helper') }}</span>
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
    var amount = form.find('[name="amount"]');
    var bayarText = form.find('[name="bayar_text"]');

    bayarText.on('change keyup blur paste', function(e) {
        var value = numeral(e.target.value);
        bayarText.val(value.format('0,0'));
        amount.val(value.value()).trigger('change');
    }).trigger('change');
});
</script>
@endsection