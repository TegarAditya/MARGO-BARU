@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.invoice.title_singular') }}
    </div>

    <div class="card-body">
        
        @if (session()->has('error-message'))
            <p class="text-danger">
                {{session()->get('error-message')}}
            </p>
        @endif

        <form method="POST" action="{{ route("admin.invoices.update", [$invoice->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="no_faktur">{{ trans('cruds.invoice.fields.no_faktur') }}</label>
                        <input class="form-control {{ $errors->has('no_faktur') ? 'is-invalid' : '' }}" type="text" name="no_faktur" id="no_faktur" value="{{ $invoice->no_faktur }}" readonly>
                        @if($errors->has('no_faktur'))
                            <span class="text-danger">{{ $errors->first('no_faktur') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.invoice.fields.no_faktur_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="date">{{ trans('cruds.invoice.fields.date') }}</label>
                        <input class="form-control date {{ $errors->has('date') ? 'is-invalid' : '' }}" type="text" name="date" id="date" value="{{ old('date', $invoice->date) }}" required>
                        @if($errors->has('date'))
                            <span class="text-danger">{{ $errors->first('date') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.invoice.fields.date_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="no_suratjalan">{{ trans('cruds.invoice.fields.delivery_order') }}</label>
                        <input class="form-control" type="text" name="no_suratjalan" value="{{ $invoice->delivery_order->no_suratjalan }}" readonly>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="salesperson">{{ trans('cruds.invoice.fields.salesperson') }}</label>
                        <input class="form-control" type="text" name="salesperson" value="{{ $invoice->salesperson->name }}" readonly>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label for="note">{{ trans('cruds.invoice.fields.note') }}</label>
                        <textarea class="form-control {{ $errors->has('note') ? 'is-invalid' : '' }}" name="note" id="note">{{ old('note', $invoice->note) }}</textarea>
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
                @foreach ($invoice_item as $item)
                    @php
                        $product = $item->product;
                    @endphp
                    <div class="item-product" id="product-{{$item->product_id}}">
                        <div class="row">
                            <div class="col-4 align-self-center">
                                <h6 class="text-sm product-name mb-1">({{ $product->book_type }}) {{ $product->short_name }}</h6>
                                <p class="mb-0 text-sm">
                                    Code : <strong>{{ $product->code }}</strong>
                                </p>
                                <p class="mb-0 text-sm">
                                    Jenjang - Cover - Isi : <strong>{{ $product->jenjang->name }} - {{ $product->book->cover->name }} - {{ $product->book->kurikulum->name }}</strong>
                                </p>
                                <p class="mb-0 text-sm">
                                    Payment Type: <strong>{{ strtoupper($item->delivery_order->payment_type) }}</strong>
                                </p>
                            </div>
                            <input type="hidden" name="invoice_items[]" value="{{ $item->id }}">
                            <input type="hidden" name="products[]" value="{{ $product->id }}">
                            <div class="col offset-1 row align-items-end align-self-center">
                                <div class="col" style="max-width: 210px">
                                    <p class="mb-0 text-sm">Price</p>
                                    <div class="form-group text-field m-0">
                                        <div class="text-field-input px-2 py-0 pr-3">
                                            <span class="text-sm mr-1">Rp</span>
                                            <input class="price" type="hidden" name="prices[]" value="{{ $product->price }}">
                                            <input class="form-control text-right price_text" type="text" name="price_text[]" value="{{ angka($product->price)}}" readonly tabindex="-1">
                                            <label class="text-field-border"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col" style="max-width: 210px">
                                    <p class="mb-0 text-sm"><b>Discount</b></p>
                                    <div class="form-group text-field m-0">
                                        <div class="text-field-input px-2 py-0 pr-3">
                                            <span class="text-sm mr-1">Rp</span>
                                            <input class="diskon" type="hidden" name="diskons[]" data-max="{{ $product->price }}" value="{{ $item->discount }}">
                                            <input class="form-control text-right diskon_text" type="text" name="diskon_text[]" value="{{ angka($item->discount) }}" required>
                                            <label class="text-field-border"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col" style="max-width: 120px">
                                    <p class="mb-0 text-sm">Quantity</p>
                                    <div class="form-group text-field m-0">
                                        <div class="text-field-input px-2 py-0">
                                            <input class="quantity" type="hidden" name="quantities[]" value="{{ $item->quantity }}">
                                            <input class="form-control text-center quantity_text" type="text" name="quantity_text[]" value="{{ angka($item->quantity) }}" readonly tabindex="-1">
                                            <label class="text-field-border"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col text-right pl-5">
                                    <input class="subtotal" type="hidden" name="subtotals[]" value="{{ $item->total }}">
                                    <p class="text-sm mb-0"><b>Subtotal</b></p>
                                    <p class="m-0 product-subtotal">{{ money($item->total) }}</p>
                                </div>
                            </div>
                        </div>
                        <hr style="margin: 1em -15px;border-color:#ccc" />
                    </div>
                @endforeach
                <div class="row mt-3 pt-2 ml-2">
                    <div class="col text-right">
                        <p class="mb-1">
                            <span class="text-sm"><b>Grand Total</b></span>
                            <br />
                            <input class="subtotal" type="hidden" name="nominal" id="nominal" value="{{ $invoice->nominal }}">
                            <strong class="product-total">{{ money($invoice->nominal) }}</strong>
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
                var max = diskon.data('max');
                var valueNum = value.value();
                if (valueNum < 0) {
                    diskonText.val(0);
                    diskon.val(0);
                } else if (valueNum > max) {
                    var maxNum = numeral(max);
                    diskonText.val(maxNum.format('0,0'));
                    diskon.val(maxNum.value());
                } else {
                    diskonText.val(value.format('0,0'));
                    diskon.val(value.value());
                }
                calculatePrice();
            }).trigger('change');
        });

        productForm.on('click', '.product-delete', function() {
            var productId = $(this).data('product-id');
            $('#product-' + productId).remove();
            calculatePrice();
        });

        function calculatePrice () {
            var total = 0;

            productForm.children().each(function(i, item) {
                var product = $(item);
                var price = parseInt(product.find('.price').val() || 0);
                var diskon = parseInt(product.find('.diskon').val() || 0);
                var quantity = parseInt(product.find('.quantity').val() || 0);
                subtotal = (price - diskon) * quantity;
                product.find('.product-subtotal').html(numeral(subtotal).format('$0,0'));

                total += subtotal
            });

            productTotal.html(numeral(total).format('$0,0'));
            productForm.find('[name="nominal"]').val(total);
        };
    });
})(jQuery, window.numeral);
</script>
@endsection
