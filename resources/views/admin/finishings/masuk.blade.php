@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h1>Formulir Buku Masuk Finishing</h1>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.finishings.masukstore") }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="no_spk">No SPK</label>
                        <input class="form-control {{ $errors->has('no_spk') ? 'is-invalid' : '' }}" type="text" name="no_spk" id="no_spk" value="{{ old('no_spk') }}" required>
                        @if($errors->has('no_spk'))
                            <span class="text-danger">{{ $errors->first('no_spk') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.finishing.fields.no_spk_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="date">{{ trans('cruds.finishing.fields.date') }}</label>
                        <input class="form-control date {{ $errors->has('date') ? 'is-invalid' : '' }}" type="text" name="date" id="date" value="{{ old('date', $today) }}" required>
                        @if($errors->has('date'))
                            <span class="text-danger">{{ $errors->first('date') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.finishing.fields.date_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="vendor_id">{{ trans('cruds.finishing.fields.vendor') }}</label>
                        <select class="form-control select2 {{ $errors->has('vendor') ? 'is-invalid' : '' }}" name="vendor_id" id="vendor_id" required>
                            @foreach($vendors as $id => $entry)
                                <option value="{{ $id }}" {{ old('vendor_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('vendor'))
                            <span class="text-danger">{{ $errors->first('vendor') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.finishing.fields.vendor_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="jenjang_id">{{ trans('cruds.bookVariant.fields.jenjang') }}</label>
                        <select class="form-control select2 {{ $errors->has('jenjang') ? 'is-invalid' : '' }}" name="jenjang_id" id="jenjang_id" required>
                            @foreach($jenjangs as $id => $entry)
                                <option value="{{ $id }}" {{ old('jenjang_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('jenjang'))
                            <span class="text-danger">{{ $errors->first('jenjang') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.bookVariant.fields.jenjang_helper') }}</span>
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
                    url: "{{ route('admin.book-variants.getListFinishing') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                            vendor: $('#vendor_id').val(),
                            jenjang: $('#jenjang_id').val()
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

            var productInfo = $('<span>' + product.text + '</span><br><small class="stock-info">' + product.finishing_spk + '</small><br><small class="stock-info">' + product.name + '</small>');
            return productInfo;
        }

        function formatProductSelection(product) {
            return product.text;
        }

        $('#product-search').on('select2:select', function(e) {
            var productId = e.params.data.finishing_item_id;

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
                url: "{{ route('admin.book-variants.getListFinishingInfo') }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    id: productId,
                },
                success: function(item) {
                    var formHtml = `
                        <div class="item-product" id="product-${item.id}">
                            <div class="row">
                                <div class="col-6 align-self-center">
                                    <h6 class="text-sm product-name mb-1">(${item.product.book_type}) ${item.product.short_name}</h6>
                                    <p class="mb-0 text-sm">
                                        Code : <strong>${item.product.code}</strong>
                                    </p>
                                    <p class="mb-0 text-sm">
                                        Jenjang - Kurikulum : <strong>${item.product.jenjang.name} - ${item.product.kurikulum.name}</strong>
                                    </p>
                                    <p class="mb-0 text-sm">
                                        <strong>No SPK Finishing : ${item.finishing.no_spk}</strong>
                                    </p>
                                    <p class="mb-0 text-sm">
                                        <strong>SPK Finishing : ${item.estimasi}</strong>
                                    </p>
                                    <p class="mb-0 text-sm">
                                        <strong>Realisasi Sekarang : ${item.quantity}</strong>
                                    </p>
                                    <p class="mb-0 text-sm">
                                        <strong>Sisa : ${item.estimasi - item.quantity}</strong>
                                    </p>
                                </div>
                                <div class="col row align-items-end align-self-center">
                                    <input type="hidden" name="products[]" value="${item.product_id}">
                                    <input type="hidden" name="finishing_items[]" value="${ item.id }">
                                    <div class="col" style="max-width: 160px">
                                        <p class="mb-0 text-sm">Realisasi</p>
                                        <div class="form-group text-field m-0">
                                            <div class="text-field-input px-2 py-0">
                                                <input class="quantity" type="hidden" name="quantities[]" value="1">
                                                <input class="form-control text-center quantity_text" type="text" name="quantity_text[]" value="1" required>
                                                <label class="text-field-border"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col" style="max-width: 200px">
                                        <div class="form-group text-field m-0">
                                            <div class="text-field-input px-2 py-1">
                                                <input class="done" type="hidden" name="done[]" value="${ item.done }">
                                                <input class="status bootstrap-switch" type="checkbox" ${ item.done == 1 ? 'checked' : ''  } tabindex="-1" value="1" data-on-text="DONE" data-off-text="N/Y">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-auto pl-5">
                                        <button type="button" class="btn btn-danger btn-sm product-delete" data-product-id="${item.id}" tabIndex="-1">
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

                    $(".status").bootstrapSwitch();

                    productItem.each(function(index, item) {
                        var product = $(item);
                        var quantity = product.find('.quantity');
                        var quantityText = product.find('.quantity_text');
                        var status = product.find('.status');
                        var done = product.find('.done');

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

                        status.on('switchChange.bootstrapSwitch', function (event, state) {
                            if (state) {
                                done.val(1);
                            } else {
                                done.val(0);
                            }
                        });
                    });
                },
                error: function(xhr, status, error) {
                    console.log(error);
                }
            });
        });

        $('#product-form').on('click', '.product-delete', function() {
            var productId = $(this).data('product-id');
            $('#product-' + productId).remove();
        });
    });
</script>
@endsection
