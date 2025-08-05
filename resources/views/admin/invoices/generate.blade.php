@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h1>Formulir Faktur Penjualan</h1>
    </div>

    <div class="card-body">

        @if (session()->has('error-message'))
            <p class="text-danger">
                {{session()->get('error-message')}}
            </p>
        @endif

        <form class="form-prevent-multiple-submits" method="POST" action="{{ route("admin.invoices.store") }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="delivery" value="{{ $delivery->id }}">
            <div class="row mb-3">
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="no_faktur">{{ trans('cruds.invoice.fields.no_faktur') }}</label>
                        <input class="form-control {{ $errors->has('no_faktur') ? 'is-invalid' : '' }}" type="text" name="no_faktur" id="no_faktur" value="{{ old('no_faktur', $no_faktur) }}" readonly>
                        @if($errors->has('no_faktur'))
                            <span class="text-danger">{{ $errors->first('no_faktur') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.invoice.fields.no_faktur_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="date">{{ trans('cruds.invoice.fields.date') }}</label>
                        <input class="form-control date {{ $errors->has('date') ? 'is-invalid' : '' }}" type="text" name="date" id="date" value="{{ old('date', $today) }}" required>
                        @if($errors->has('date'))
                            <span class="text-danger">{{ $errors->first('date') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.invoice.fields.date_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="no_suratjalan">{{ trans('cruds.invoice.fields.delivery_order') }}</label>
                        <input class="form-control" type="text" name="no_suratjalan" value="{{ $delivery->no_suratjalan }}" readonly>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="date">Tanggal Surat Jalan</label>
                        <input class="form-control date {{ $errors->has('date') ? 'is-invalid' : '' }}" type="text" name="date_do" id="date_do" value="{{ $delivery->date }}" readonly>
                        @if($errors->has('date'))
                            <span class="text-danger">{{ $errors->first('date') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.invoice.fields.date_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="salesperson">{{ trans('cruds.invoice.fields.salesperson') }}</label>
                        <input class="form-control" type="text" name="salesperson" value="{{ $delivery->salesperson->short_name }}" readonly>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="salesperson">{{ trans('cruds.invoice.fields.semester') }}</label>
                        <input class="form-control" type="text" name="semester" value="{{ $delivery->semester->name }}" readonly>
                    </div>
                </div>
                {{-- <div class="col-12">
                    <div class="form-group">
                        <label for="note">{{ trans('cruds.invoice.fields.note') }}</label>
                        <textarea class="form-control {{ $errors->has('note') ? 'is-invalid' : '' }}" name="note" id="note">{{ old('note') }}</textarea>
                        @if($errors->has('note'))
                            <span class="text-danger">{{ $errors->first('note') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.invoice.fields.note_helper') }}</span>
                    </div>
                </div> --}}
            </div>
            <hr style="margin: .5em -15px;border-color:#ccc" />
            <div class="row mb-2">
                <div class="col py-1">
                    <h5 class="product-group-title"><b>Product</b></h5>
                </div>
            </div>
            <div id="product-form">
                @php
                    $nominal = 0
                @endphp
                @foreach ($delivery_item as $item)
                    @php
                        $product = $item->product;
                        $subtotal = $product->price * $item->quantity;
                        $nominal += $subtotal;
                    @endphp
                    <div class="item-product" id="product-{{$item->product_id}}">
                        <div class="row">
                            <div class="col-4 align-self-center">
                                <h6 class="text-sm product-name mb-1">({{ $product->book_type }}) {{ $product->short_name }}</h6>
                                <p class="mb-0 text-sm">
                                    Code : <strong>{{ $product->code }}</strong>
                                </p>
                                <p class="mb-0 text-sm">
                                    Jenjang - Kurikulum :
                                    <strong @if(!$product->book) class="text-danger" @endif>
                                        {{ $product->jenjang->name }}
                                        -
                                        {{ optional($product->book)->cover->name ?? $product->cover->name }}
                                        -
                                        {{ optional($product->book)->kurikulum->name ?? $product->kurikulum->name}}
                                    </strong>
                                </p>
                            </div>
                            <input type="hidden" name="delivery_items[]" value="{{ $item->id }}">
                            <input type="hidden" name="products[]" value="{{ $product->id }}">
                            <div class="col offset-1 row align-items-end align-self-center">
                                <div class="col" style="max-width: 210px">
                                    <p class="mb-0 text-sm">Price</p>
                                    <div class="form-group text-field m-0">
                                        <div class="text-field-input px-2 py-0 pr-3">
                                            <span class="text-sm mr-1">Rp</span>
                                            <input class="price" type="hidden" name="prices[]" value="{{ $product->price }}">
                                            <input class="form-control text-right price_text" type="text" name="price_text[]" value="{{ angka($product->price)}}" tabindex="-1">
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
                                <div class="col" style="max-width: 210px">
                                    <p class="mb-0 text-sm">Discount</p>
                                    <div class="form-group text-field m-0">
                                        <div class="text-field-input px-2 py-0 pr-3">
                                            <span class="text-sm mr-1">Rp</span>
                                            <input class="diskon" type="hidden" name="diskons[]" data-max="{{ $product->price }}" value="0">
                                            <input class="form-control text-right diskon_text" type="text" name="diskon_text[]" value="0" required>
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
                                    <input class="subdiskon" type="hidden" name="subdiscounts[]" value="0">
                                    <p class="text-sm mb-0"><b>Discount</b></p>
                                    <p class="m-0 product-subdiskon">{{ money(0) }}</p>
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
                            <input class="total_price" type="hidden" name="total_price" id="total_price" value="{{ $nominal }}">
                            <strong class="product-total-price">{{ money($nominal) }}</strong>
                        </p>
                    </div>
                </div>
                <div class="row pt-2 ml-2">
                    <div class="col-10 text-right">
                        <span class="text-sm"><b>Discount</b></span>
                    </div>
                    <div class="col-2 text-right">
                        <p class="mb-1">
                            <input class="total_diskon" type="hidden" name="total_diskon" id="total_diskon" value="0">
                            <strong class="product-total-diskon">{{ money(0) }}</strong>
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
                    <button type="submit" class="btn btn-primary form-prevent-multiple-submits">Simpan Faktur</button>
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
            var price = product.find('.price');
            var priceText = product.find('.price_text');

            diskonText.on('input change', function(e) {
                var value = numeral(e.target.value);

                diskonText.val(value.format('0,0'));
                diskon.val(value.value()).trigger('change');
                calculatePrice();
            }).trigger('change');

            diskon.on('change input', function(e) {
                var el = $(e.currentTarget);
                var max = parseInt(el.data('max'));
                var valueNum = parseInt(el.val());

                if (valueNum < 0) {
                    el.val(0);
                    diskonText.val(0).trigger('change');
                }

                if (valueNum > max) {
                    el.val(max);
                    diskonText.val(max).trigger('change');
                }
            }).trigger('change');

            priceText.on('input change', function(e) {
                var value = numeral(e.target.value);

                priceText.val(value.format('0,0'));
                price.val(value.value()).trigger('change');
                calculatePrice();
            }).trigger('change');

            price.on('change input', function(e) {
                var el = $(e.currentTarget);
                var max = parseInt(el.data('max'));
                var valueNum = parseInt(el.val());

                if (valueNum < 0) {
                    el.val(0);
                    priceText.val(0).trigger('change');
                }

                if (valueNum > max) {
                    el.val(max);
                    priceText.val(max).trigger('change');
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
            var total_price = 0;

            productForm.children().each(function(i, item) {
                var product = $(item);
                var quantity = parseInt(product.find('.quantity').val() || 0);
                var price = parseInt(product.find('.price').val() || 0);
                var diskon = parseInt(product.find('.diskon').val() || 0);

                subprice = price * quantity;
                product.find('.product-subtotal').html(numeral(subprice).format('$0,0'));
                product.find('.subtotal').val(subprice);
                total_price += subprice;

                subdiskon = diskon * quantity;
                product.find('.product-subdiskon').html(numeral(subdiskon).format('$0,0'));
                product.find('.subdiskon').val(subdiskon);
                total_diskon += subdiskon;
            });
            // var total_price = parseInt(productForm.find('[name="total_price"]').val() || 0);
            var total = total_price - total_diskon;

            productTotalPrice.html(numeral(total_price).format('$0,0'));
            productTotalDiskon.html(numeral(total_diskon).format('$0,0'));
            productForm.find('[name="total_price"]').val(total_price);
            productForm.find('[name="total_diskon"]').val(total_diskon);
            productTotal.html(numeral(total).format('$0,0'));
            productForm.find('[name="nominal"]').val(total);
        };
    });
})(jQuery, window.numeral);
</script>
@endsection
