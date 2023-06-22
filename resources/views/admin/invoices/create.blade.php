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

        <form method="POST" action="{{ route("admin.invoices.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="no_faktur">{{ trans('cruds.invoice.fields.no_faktur') }}</label>
                        <input class="form-control {{ $errors->has('no_faktur') ? 'is-invalid' : '' }}" type="text" name="no_faktur" id="no_faktur" value="{{ old('no_faktur', '') }}" required>
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
                        <label for="delivery_order_id">{{ trans('cruds.invoice.fields.delivery_order') }}</label>
                        <select class="form-control select2 {{ $errors->has('delivery_order') ? 'is-invalid' : '' }}" name="delivery_order_id" id="delivery_order_id">
                            @foreach($delivery_orders as $id => $entry)
                                <option value="{{ $id }}" {{ old('delivery_order_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('delivery_order'))
                            <span class="text-danger">{{ $errors->first('delivery_order') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.invoice.fields.delivery_order_helper') }}</span>
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
                <div class="col-12">
                    <div class="form-group">
                        <label for="note">{{ trans('cruds.invoice.fields.note') }}</label>
                        <textarea class="form-control {{ $errors->has('note') ? 'is-invalid' : '' }}" name="note" id="note">{{ old('note') }}</textarea>
                        @if($errors->has('note'))
                            <span class="text-danger">{{ $errors->first('note') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.invoice.fields.note_helper') }}</span>
                    </div>
                </div>
            </div>
            <hr style="margin: .5em -15px;border-color:#ccc" />
            <div class="row mb-2">
                <div class="col py-1">
                    <h5 class="product-group-title"><b>Product</b></h5>
                </div>
            </div>
            <div id="product-form">
                <div class="item-product" id="product-1">
                    <div class="row">
                        <div class="col-4 align-self-center">
                            <h6 class="text-sm product-name mb-1">(${product.book_type}) ${product.short_name}</h6>
                            <p class="mb-0 text-sm">
                                Code : <strong>${product.code}</strong>
                            </p>
                            <p class="mb-0 text-sm">
                                Jenjang - Cover - Isi : <strong>${product.jenjang.name} - ${product.book.cover.name} - ${product.book.kurikulum.name}</strong>
                            </p>
                            <p class="mb-0 text-sm">
                                <strong>ESTIMASI : ${product.estimasi}</strong>
                            </p>
                            <p class="mb-0 text-sm">
                                <strong>TERKIRIM : ${product.terkirim}</strong>
                            </p>
                        </div>
                        <input type="hidden" name="deliveryitems[]" value="1">
                        <input type="hidden" name="products[]" value="${product.id}">
                        <div class="col offset-1 row align-items-end align-self-center">
                            <div class="col" style="max-width: 200px">
                                <p class="mb-0 text-sm">Price</p>
                                <div class="form-group text-field m-0">
                                    <div class="text-field-input px-2 py-0 pr-3">
                                        <span class="text-sm mr-1">Rp</span>
                                        <input class="price" type="hidden" name="prices[]" value="1">
                                        <input class="form-control text-right price_text" type="text" name="price_text[]" value="1" readonly>
                                        <label class="text-field-border"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col" style="max-width: 120px">
                                <p class="mb-0 text-sm">Quantity</p>
                                <div class="form-group text-field m-0">
                                    <div class="text-field-input px-2 py-0">
                                        <input class="quantity" type="hidden" name="quantities[]" value="1" readonly>
                                        <input class="form-control text-center quantity_text" type="text" name="quantity_text[]" value="1" required>
                                        <label class="text-field-border"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col" style="max-width: 200px">
                                <p class="mb-0 text-sm">Discount</p>
                                <div class="form-group text-field m-0">
                                    <div class="text-field-input px-2 py-0 pr-3">
                                        <span class="text-sm mr-1">Rp</span>
                                        <input class="diskon" type="hidden" name="diskons[]" data-max="1000000" value="1">
                                        <input class="form-control text-right diskon_text" type="text" name="diskon_text[]" value="1" required>
                                        <label class="text-field-border"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col text-right">
                                <p class="text-sm mb-0">Subtotal</p>
                                <p class="m-0 product-subtotal">Rp 3.900.000</p>
                            </div>

                            <div class="col-auto pl-5">
                                <button type="button" class="btn btn-danger btn-sm product-delete" data-product-id="${product.id}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <hr style="margin: 1em -15px;border-color:#ccc" />
                <div class="row mt-3 pt-2 ml-2">
                    <div class="col text-right">
                        <p class="mb-1">
                            <span class="text-sm">Grand Total</span>
                            <br />
                            <strong class="product-total">Rp 1.0000.0000.0000</strong>
                        </p>
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
        var productForm = $('#product-form');
        var productItem = productForm.find('.item-product');
        var productTotal = productForm.find('.product-total');

        productItem.each(function(index, item) {
            var product = $(item);
            var diskon = product.find('.diskon');
            var diskonText = product.find('.diskon_text');

            diskonText.on('input change', function(e) {
                var value = numeral(e.target.value);

                diskonText.val(value.format('0,0'));
                diskon.val(value.value()).trigger('change');
            }).trigger('change');

            diskon.on('change', function(e) {
                var el = $(e.currentTarget);
                var valueNum = parseInt(el.val());
                if (valueNum < 1) {
                    el.val(1);
                    diskonText.val(1).trigger('change');
                }
            }).trigger('change');
        });

        $('#product-form').on('click', '.product-delete', function() {
            var productId = $(this).data('product-id');
            $('#product-' + productId).remove();
        });

        var calculatePrice = function() {
            var total = 0;

            productItem.each(function(index, item) {
                var product = $(item);
                var diskon = product.find('.diskon').val() || 0;
                subtotal = (1000 * diskon);
                product.find('.product-subtotal').html(numeral(subtotal).format('$0,0'));

                total += subtotal
            });

            productTotal.html(numeral(total).format('$0,0'));
        };
    });
})(jQuery, window.numeral);
</script>
@endsection
