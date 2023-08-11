@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.deliveryPlate.title_singular') }}
    </div>

    <div class="card-body">
        @if (session()->has('error-message'))
            <p class="text-danger">
                {{session()->get('error-message')}}
            </p>
        @endif

        <form method="POST" action="{{ route("admin.delivery-plates.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="no_suratjalan">{{ trans('cruds.deliveryPlate.fields.no_suratjalan') }}</label>
                        <input class="form-control {{ $errors->has('no_suratjalan') ? 'is-invalid' : '' }}" type="text" name="no_suratjalan" id="no_suratjalan" value="{{ old('no_suratjalan', $no_suratjalan) }}" readonly>
                        @if($errors->has('no_suratjalan'))
                            <span class="text-danger">{{ $errors->first('no_suratjalan') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.deliveryPlate.fields.no_suratjalan_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="date">{{ trans('cruds.deliveryPlate.fields.date') }}</label>
                        <input class="form-control date {{ $errors->has('date') ? 'is-invalid' : '' }}" type="text" name="date" id="date" value="{{ old('date', $today) }}" required>
                        @if($errors->has('date'))
                            <span class="text-danger">{{ $errors->first('date') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.deliveryPlate.fields.date_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label>{{ trans('cruds.bookVariant.fields.type') }}</label>
                        <select class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type" id="type">
                            <option value disabled {{ old('type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                            @foreach(App\Models\PlatePrint::TYPE_SELECT as $key => $label)
                                <option value="{{ $key }}" {{ old('type', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <div id="internal">
                            <label for="vendor_id">{{ trans('cruds.deliveryPlate.fields.vendor') }}</label>
                            <select class="form-control select2 {{ $errors->has('vendor') ? 'is-invalid' : '' }}" name="vendor_id" id="vendor_id">
                                @foreach($vendors as $id => $entry)
                                    <option value="{{ $id }}" {{ old('vendor_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="external" style="display: none">
                            <label for="customer">{{ trans('cruds.deliveryPlate.fields.customer') }}</label>
                            <input class="form-control {{ $errors->has('customer') ? 'is-invalid' : '' }}" type="text" name="customer" id="customer" value="{{ old('customer', '') }}">
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label for="note">{{ trans('cruds.deliveryPlate.fields.note') }}</label>
                        <textarea class="form-control {{ $errors->has('note') ? 'is-invalid' : '' }}" name="note" id="note">{{ old('note') }}</textarea>
                        @if($errors->has('note'))
                            <span class="text-danger">{{ $errors->first('note') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.deliveryPlate.fields.note_helper') }}</span>
                    </div>
                </div>
            </div>
            <hr style="margin: .5em -15px;border-color:#ccc" />
            <div class="row mb-4">
                <div class="col-12">
                    <div class="form-group">
                        <label for="product-search">Cari Pesanan</label>
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
                    url: "{{ route('admin.delivery-plates.getPlateItems') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                            type: $('#type').val(),
                            vendor: $('#vendor_id').val(),
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

        $('#type').on('change', function() {
            if ($(this).val() == 'internal') {
                $('#external').hide();
                $('#internal').show();
                $('#vendor_id').val(null).trigger('change');
            } else {
                $('#internal').hide();
                $('#external').show();
                $('#customer').val(null).trigger('change');
            }
        });

        function formatProduct(product) {
            if (!product.id) {
                return product.text;
            }

            var productInfo = $('<span>' + product.text + '</span><br><small class="stock-info">' + product.mapel + '</small><br><small class="stock-info">Kirim: ' + product.quantity + '</small><br><small class="stock-info">No SPK: ' + product.spk + '</small>');
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
                    title: 'Item Sudah Ditambahkan!',
                    showConfirmButton: false,
                    timer: 2000
                });

                $('#product-search').val(null).trigger('change');
                return;
            }

            $.ajax({
                url: "{{ route('admin.delivery-plates.getInfoPlateItem') }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    id: productId,
                },
                success: function(item) {
                    var formHtml = `
                        <div class="item-product" id="product-${item.id}">
                            <div class="row">
                                <div class="col-8 align-self-center">
                                    <h6 class="text-sm product-name mb-1">${item.product ? item.product.name : item.product_text }</h6>
                                    <p class="mb-0 text-sm">
                                        Plate : <strong>${item.plate.name}</strong>
                                    </p>
                                    <p class="mb-0 text-sm">
                                        No SPK : <strong>${item.plate_print.no_spk}</strong>
                                    </p>
                                    <p class="mb-0 text-sm">
                                        Jumlah Plate: <strong>${item.estimasi}</strong>
                                    </p>
                                </div>
                                <div class="col offset-1 row align-items-end align-self-center">
                                    <input type="hidden" name="plate_items[]" value="${item.id}">
                                    <div class="col" style="min-width: 100px">
                                        <p class="mb-0 text-sm">Jumlah Plate</p>
                                        <div class="form-group text-field m-0">
                                            <div class="text-field-input px-2 py-0">
                                                <input class="plate_quantity" type="hidden" name="plate_quantities[]" value="${item.estimasi}">
                                                <input class="form-control text-center plate_quantity_text" type="text" name="plate_quantity_text[]" value="${item.estimasi}" readonly>
                                                <label class="text-field-border"></label>
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

                    productItem.each(function(index, item) {
                        var product = $(item);
                        var plateQuantity = product.find('.plate_quantity');
                        var plateQuantityText = product.find('.plate_quantity_text');

                        plateQuantityText.on('input', function(e) {
                            var value = numeral(e.target.value);

                            plateQuantityText.val(value.format('0,0'));
                            plateQuantity.val(value.value()).trigger('change');
                        }).trigger('change');

                        plateQuantity.on('change', function(e) {
                            var el = $(e.currentTarget);
                            var valueNum = parseInt(el.val());
                            if (valueNum < 1) {
                                el.val(1);
                                plateQuantityText.val(1).trigger('change');
                            }
                        }).trigger('change');
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
