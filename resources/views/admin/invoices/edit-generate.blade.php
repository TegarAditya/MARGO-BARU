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
            </div>
            <hr style="margin: .5em -15px;border-color:#ccc" />
            <div class="row mb-2">
                <div class="col py-1">
                    <h5 class="product-group-title"><b>Product</b></h5>
                </div>
            </div>
            <div id="product-form">
                @php
                    $nominal = 0;
                    $total = 0;
                    $total_discount = 0;
                @endphp
                @foreach ($invoice_item as $item)
                    @php
                        $product = $item->product;
                        $item_delivery = $delivery_item->where('product_id', $item->product_id)->first();

                        if ($item_delivery->quantity !== $item->quantity) {
                            $quantity = $item_delivery->quantity;
                        } else {
                            $quantity = $item->quantity;
                        }

                        $discount = $item->discount;

                        $subtotal = $product->price * $quantity;
                        $total += $subtotal;

                        $subdiscount = $discount * $quantity;
                        $total_discount += $subdiscount;

                        $nominal = $total - $total_discount;
                        @endphp
                    <div class="item-product" id="product-{{$item->product_id}}">
                        <div class="row">
                            <div class="col-4 align-self-center">
                                <h6 class="text-sm product-name mb-1">({{ $product->book_type }}) {{ $product->short_name }}</h6>
                                <p class="mb-0 text-sm">
                                    Code : <strong>{{ $product->code }}</strong>
                                </p>
                                <p class="mb-0 text-sm">
                                    Jenjang - Kurikulum : <strong>{{ $product->jenjang->name }} - {{ $product->book->cover->name }} - {{ $product->book->kurikulum->name }}</strong>
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
                                <div class="col" style="max-width: 120px">
                                    <p class="mb-0 text-sm">Quantity</p>
                                    <div class="form-group text-field m-0">
                                        <div class="text-field-input px-2 py-0">
                                            <input class="quantity" type="hidden" name="quantities[]" value="{{ $quantity }}">
                                            <input class="form-control text-center quantity_text" type="text" name="quantity_text[]" value="{{ angka($quantity) }}" readonly tabindex="-1">
                                            <label class="text-field-border"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col" style="max-width: 210px">
                                    <p class="mb-0 text-sm"><b>Discount</b></p>
                                    <div class="form-group text-field m-0">
                                        <div class="text-field-input px-2 py-0 pr-3">
                                            <span class="text-sm mr-1">Rp</span>
                                            <input class="diskon" type="hidden" name="diskons[]" data-max="{{ $product->price }}" value="{{ $discount }}">
                                            <input class="form-control text-right diskon_text" type="text" name="diskon_text[]" value="{{ angka($discount) }}" required>
                                            <label class="text-field-border"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col text-right pl-2">
                                    <input class="subtotal" type="hidden" name="subtotals[]" value="{{ $subtotal }}">
                                    <p class="text-sm mb-0"><b>Total</b></p>
                                    <p class="m-0 product-subtotal">{{ money($subtotal) }}</p>
                                </div>
                                <div class="col text-right pl-2">
                                    <input class="subdiskon" type="hidden" name="subdiscounts[]" value="{{ $subdiscount }}">
                                    <p class="text-sm mb-0"><b>Discount</b></p>
                                    <p class="m-0 product-subdiskon">{{ money($subdiscount) }}</p>
                                </div>
                            </div>
                        </div>
                        <hr style="margin: 1em -15px;border-color:#ccc" />
                    </div>
                @endforeach
                <div class="row mt-3 pt-2 ml-2">
                    <div class="col-md-10 text-right">
                        <span class="text-sm"><b>Subtotal</b></span>
                    </div>
                    <div class="col-2 text-right">
                        <p class="mb-1">
                            <input class="total_price" type="hidden" name="total_price" id="total_price" value="{{ $total }}">
                            <strong class="product-total-price">{{ money($total) }}</strong>
                        </p>
                    </div>
                </div>
                <div class="row pt-2 ml-2">
                    <div class="col-10 text-right">
                        <span class="text-sm"><b>Discount</b></span>
                    </div>
                    <div class="col-2 text-right">
                        <p class="mb-1">
                            <input class="total_diskon" type="hidden" name="total_diskon" id="total_diskon" value="{{ $total_discount }}">
                            <strong class="product-total-diskon">{{ money($total_discount) }}</strong>
                        </p>
                    </div>
                </div>
                <div class="row mb-3 pt-2 ml-2">
                    <div class="col-10 text-right">
                        <span class="text-sm"><b>Grand Total</b></span>
                    </div>
                    <div class="col-2 text-right">
                        <p class="mb-1">
                            <input class="subtotal" type="hidden" name="nominal" id="nominal" value="{{ $nominal }}">
                            <strong class="product-total-nominal">{{ money($nominal) }}</strong>
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
        var productTotal = productForm.find('.product-total-nominal');
        var productTotalPrice = productForm.find('.product-total-price');
        var productTotalDiskon = productForm.find('.product-total-diskon');

        productItem.each(function(index, item) {
            var product = $(item);
            var diskon = product.find('.diskon');
            var diskonText = product.find('.diskon_text');

            diskonText.on('input change', function(e) {
                var value = numeral(e.target.value);

                diskonText.val(value.format('0,0'));
                diskon.val(value.value());
                calculatePrice();
            }).trigger('change');

            diskon.on('change', function(e) {
                var el = $(e.currentTarget);
                var max = parseInt(el.data('max'));
                console.log(max);
                var valueNum = parseInt(el.val());
                if (valueNum < 1 ) {
                    el.val(0);
                    diskonText.val(0);
                }else if (valueNum > max) {
                    el.val(max);
                    diskonText.val(max);
                }
            }).trigger('change');
        });

        productForm.on('click', '.product-delete', function() {
            var productId = $(this).data('product-id');
            $('#product-' + productId).remove();
            calculatePrice();
        });

        function calculatePrice () {
            var total_diskon = 0;

            productForm.children().each(function(i, item) {
                var product = $(item);
                var quantity = parseInt(product.find('.quantity').val() || 0);
                var diskon = parseInt(product.find('.diskon').val() || 0);

                subdiskon = diskon * quantity;
                product.find('.product-subdiskon').html(numeral(subdiskon).format('$0,0'));
                product.find('.subdiskon').val(subdiskon);
                total_diskon += subdiskon;
            });
            var total_price = parseInt(productForm.find('[name="total_price"]').val() || 0);
            var total = total_price - total_diskon;

            productTotalDiskon.html(numeral(total_diskon).format('$0,0'));
            productForm.find('[name="total_diskon"]').val(total_diskon);
            productTotal.html(numeral(total).format('$0,0'));
            productForm.find('[name="nominal"]').val(total);
        };
    });
})(jQuery, window.numeral);
</script>
@endsection
