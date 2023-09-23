@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.stockAdjustment.title_singular') }}
    </div>

    <div class="card-body">
        @if (session()->has('error-message'))
            <p class="text-danger">
                {{session()->get('error-message')}}
            </p>
        @endif

        <form method="POST" action="{{ route("admin.stock-adjustments.update", [$stockAdjustment->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <input type="hidden" name="adjustment_id" id="adjustment_id" value="{{ $stockAdjustment->id }}">
            <input type="hidden" name="type" id="type" value="{{ $stockAdjustment->type }}">
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="date">{{ trans('cruds.stockAdjustment.fields.date') }}</label>
                        <input class="form-control date {{ $errors->has('date') ? 'is-invalid' : '' }}" type="text" name="date" id="date" value="{{ old('date', $stockAdjustment->date) }}" required>
                        @if($errors->has('date'))
                            <span class="text-danger">{{ $errors->first('date') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.stockAdjustment.fields.date_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required">{{ trans('cruds.stockAdjustment.fields.operation') }}</label>
                        <select class="form-control {{ $errors->has('operation') ? 'is-invalid' : '' }}" name="operation" id="operation" disabled>
                            <option value disabled {{ old('operation', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                            @foreach(App\Models\StockAdjustment::OPERATION_SELECT as $key => $label)
                                <option value="{{ $key }}" {{ old('operation', $stockAdjustment->operation) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('operation'))
                            <span class="text-danger">{{ $errors->first('operation') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.stockAdjustment.fields.operation_helper') }}</span>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label class="required" for="reason">{{ trans('cruds.stockAdjustment.fields.reason') }}</label>
                        <input class="form-control {{ $errors->has('reason') ? 'is-invalid' : '' }}" type="text" name="reason" id="reason" value="{{ old('reason', $stockAdjustment->reason) }}" required>
                        @if($errors->has('reason'))
                            <span class="text-danger">{{ $errors->first('reason') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.stockAdjustment.fields.reason_helper') }}</span>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label for="note">{{ trans('cruds.stockAdjustment.fields.note') }}</label>
                        <textarea class="form-control {{ $errors->has('note') ? 'is-invalid' : '' }}" name="note" id="note">{{ old('note', $stockAdjustment->note) }}</textarea>
                        @if($errors->has('note'))
                            <span class="text-danger">{{ $errors->first('note') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.stockAdjustment.fields.note_helper') }}</span>
                    </div>
                </div>
            </div>
            <hr style="margin: .5em -15px;border-color:#ccc" />
            <div class="row mb-4">
                <div class="col-12">
                    <div class="form-group">
                        <label for="product-search">Book Search</label>
                        <select id="product-search" class="form-control select2" style="width: 100%;">
                            <option></option>
                        </select>
                    </div>
                </div>
            </div>
            <div id="product-form"></div>
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
        $('#product-search').select2({
            templateResult: formatProduct,
            templateSelection: formatProductSelection,
            ajax: {
                    url: "{{ route('admin.book-variants.getAdjustment') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                            adjustment: $('#adjustment_id').val(),
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                }
        });

        function formatProduct(product) {
            if (!product.id) {
                return product.text;
            }

            var productInfo = $('<span>' + product.text + '</span><br><small class="stock-info">' + product.name + '</small><br><small class="stock-info">Stock: ' + product.stock + '</small>');
            return productInfo;
        }

        function formatProductSelection(product) {
            return product.text;
        }

        $('#product-search').on('select2:select', function(e) {
            var productId = e.params.data.id;

            if ($('#product-' + productId).length > 0) {
                // Product is already added, show an error message using SweetAlert
                Swal.fire({
                    icon: 'error',
                    title: 'Buku Sudah Ditambahkan!',
                    showConfirmButton: false,
                    timer: 2000
                });

                $('#product-search').val(null).trigger('change');
                return;
            }

            $.ajax({
                url: "{{ route('admin.book-variants.getInfoAdjustment') }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    id: productId,
                    adjustment: $('#adjustment_id').val(),
                },
                success: function(product) {
                    var formHtml = `
                        <div class="item-product" id="product-${product.id}">
                            <div class="row">
                                <div class="col-8 align-self-center">
                                    <h6 class="text-sm product-name mb-1">(${product.book_type}) ${product.short_name}</h6>
                                    <p class="mb-0 text-sm">
                                        Code : <strong>${product.code}</strong>
                                    </p>
                                    <p class="mb-0 text-sm">
                                        Jenjang - Kurikulum : <strong>${product.jenjang.name} - ${product.kurikulum.name}</strong>
                                    </p>
                                    <p class="mb-0 text-sm">
                                        <strong>STOCK : ${product.stock}</strong>
                                    </p>
                                </div>
                                <div class="col offset-1 row align-items-end align-self-center">
                                    <div class="col" style="max-width: 160px">
                                        <p class="mb-0 text-sm">Dikirim</p>
                                        <div class="form-group text-field m-0">
                                            <div class="text-field-input px-2 py-0">
                                                <input type="hidden" name="products[]" value="${product.id}">
                                                <input type="hidden" name="adjustment_details[]" value="${product.adjustment_detail_id}">
                                                <input class="quantity" type="hidden" name="quantities[]" value="${product.quantity}">
                                                <input class="form-control text-center quantity_text" type="text" name="quantity_text[]" value="${product.quantity}" required>
                                                <label class="text-field-border"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-auto pl-5">
                                        <button type="button" class="btn btn-danger btn-sm product-delete" data-product-id="${product.id}" tabIndex="-1">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <hr style="margin: 1em -15px;border-color:#ccc" />
                        </div>
                    `;
                    $('#product-form').prepend(formHtml);
                    $('#product-search').val(null).trigger('change');

                    var productForm = $('#product-form');
                    var productItem = productForm.find('.item-product');

                    productItem.each(function(index, item) {
                        var product = $(item);
                        var quantity = product.find('.quantity');
                        var quantityText = product.find('.quantity_text');

                        quantityText.on('input change', function(e) {
                            var value = numeral(e.target.value);

                            quantityText.val(value.format('0,0'));
                            quantity.val(value.value()).trigger('change');
                        }).trigger('change');

                        quantity.on('change', function(e) {
                            var el = $(e.currentTarget);
                            var valueNum = parseInt(el.val());
                            if (valueNum < 0) {
                                el.val(0);
                                quantityText.val(0).trigger('change');
                            }
                        }).trigger('change');
                    });
                },
                error: function(xhr, status, error) {
                    console.log(error);
                }
            });
        });
    });

    $('#product-form').on('click', '.product-delete', function() {
        var productId = $(this).data('product-id');
        $('#product-' + productId).remove();
    });
</script>
@endsection
