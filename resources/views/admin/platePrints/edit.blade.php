@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.platePrint.title_singular') }}
    </div>

    <div class="card-body">
        <form class="form-prevent-multiple-submits" method="POST" action="{{ route("admin.plate-prints.update", [$platePrint->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="no_spk">{{ trans('cruds.platePrint.fields.no_spk') }}</label>
                        <input class="form-control {{ $errors->has('no_spk') ? 'is-invalid' : '' }}" type="text" name="no_spk" id="no_spk" value="{{ old('no_spk', $platePrint->no_spk) }}" readonly>
                        @if($errors->has('no_spk'))
                            <span class="text-danger">{{ $errors->first('no_spk') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.platePrint.fields.no_spk_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="date">{{ trans('cruds.platePrint.fields.date') }}</label>
                        <input class="form-control date {{ $errors->has('date') ? 'is-invalid' : '' }}" type="text" name="date" id="date" value="{{ old('date', $platePrint->date) }}" required>
                        @if($errors->has('date'))
                            <span class="text-danger">{{ $errors->first('date') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.platePrint.fields.date_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="customer">{{ trans('cruds.platePrint.fields.customer') }}</label>
                        <input class="form-control {{ $errors->has('customer') ? 'is-invalid' : '' }}" type="text" name="customer" id="customer" value="{{ old('customer', $platePrint->customer) }}" required>
                        @if($errors->has('customer'))
                            <span class="text-danger">{{ $errors->first('customer') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.platePrint.fields.customer_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="bayar">{{ trans('cruds.platePrint.fields.fee') }}</label>
                        <div class="form-group text-field m-0">
                            <div class="text-field-input px-2 py-0">
                                <span class="mr-1">Rp</span>
                                <input type="hidden" id="bayar" name="bayar" value="{{ $platePrint->fee }}" />
                                <input class="form-control" type="text" id="bayar_text" name="bayar_text" min="1" value="{{ angka($platePrint->fee) }}">
                                <label for="bayar_text" class="text-field-border"></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label for="note">{{ trans('cruds.platePrint.fields.note') }}</label>
                        <textarea class="form-control {{ $errors->has('note') ? 'is-invalid' : '' }}" name="note" id="note">{{ old('note', $platePrint->note) }}</textarea>
                        @if($errors->has('note'))
                            <span class="text-danger">{{ $errors->first('note') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.platePrint.fields.note_helper') }}</span>
                    </div>
                </div>
            </div>
            <hr style="margin: .5em -15px;border-color:#ccc" />
            <div class="row mb-4">
                <div class="col-12">
                    <div class="form-group">
                        <label for="product-search">Plate Search</label>
                        <select id="product-search" class="form-control select2" style="width: 100%;">
                            <option></option>
                        </select>
                    </div>
                </div>
            </div>
            <div id="product-form">
                @php
                    $element_count = 1;
                @endphp
                @foreach ($plate_items as $item)
                    <div class="item-product" id="product-{{ $element_count }}">
                        <div class="row">
                            <div class="col-4 align-self-center">
                                <h6 class="text-sm product-name mb-1">{{ $item->plate->code . ' - '. $item->plate->name  }}</h6>
                                <p class="mb-0 text-sm">
                                    Name : <strong>{{ $item->plate->name }}</strong>
                                </p>
                                <p class="mb-0 text-sm">
                                    <strong>STOCK : {{ $item->plate->stock }}</strong>
                                </p>
                            </div>
                            <div class="col offset-1 row align-items-end align-self-center">
                                <input type="hidden" name="plate_items[]" value="{{ $item->id }}">
                                <input type="hidden" name="plates[]" value="{{ $item->plate_id }}">
                                <div class="col" style="min-width: 360px">
                                    <p class="mb-0 text-sm">Mapel</p>
                                    <div class="form-group text-field m-0">
                                        <div class="text-field-input px-2 py-0">
                                            <input class="form-control text-center" type="text" name="mapels[]" value="{{ $item->product_text }}" required>
                                            <label class="text-field-border"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col" style="min-width: 100px; max-width: 160px">
                                    <p class="mb-0 text-sm">Jumlah Pesan</p>
                                    <div class="form-group text-field m-0">
                                        <div class="text-field-input px-2 py-0">
                                            <input class="plate_quantity" type="hidden" name="plate_quantities[]" value="{{ $item->estimasi }}">
                                            <input class="form-control text-center plate_quantity_text" type="text" name="plate_quantity_text[]" value="{{ angka($item->estimasi) }}" required>
                                            <label class="text-field-border"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr style="margin: 1em -15px;border-color:#ccc" />
                    </div>
                    @php
                        $element_count++;
                    @endphp
                @endforeach
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
        var id_element = {{ $element_count }};
        var bayar = $('#bayar');
        var bayarText = $('#bayar_text');

        bayarText.on('change keyup blur paste', function(e) {
            var value = numeral(e.target.value);
            console.log(value.format('0,0'));
            bayarText.val(value.format('0,0'));
            bayar.val(value.value()).trigger('change');
        }).trigger('change');

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
                    if (valueNum < 0) {
                        el.val(0);
                        plateQuantityText.val(0).trigger('change');
                    }
                }).trigger('change');
            });

        $('#product-search').select2({
            templateResult: formatProduct,
            templateSelection: formatProductSelection,
            ajax: {
                    url: "{{ route('admin.materials.getPlates') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
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
            var product = e.params.data;
            id_element++;

            var formHtml = `
                <div class="item-product" id="product-${id_element}">
                    <div class="row">
                        <div class="col-4 align-self-center">
                            <h6 class="text-sm product-name mb-1">${product.text}</h6>
                            <p class="mb-0 text-sm">
                                Name : <strong>${product.name}</strong>
                            </p>
                            <p class="mb-0 text-sm">
                                <strong>STOCK : ${product.stock}</strong>
                            </p>
                        </div>
                        <div class="col offset-1 row align-items-end align-self-center">
                            <input type="hidden" name="plate_items[]" value="">
                            <input type="hidden" name="plates[]" value="${product.id}">
                            <div class="col" style="min-width: 360px">
                                <p class="mb-0 text-sm">Mapel</p>
                                <div class="form-group text-field m-0">
                                    <div class="text-field-input px-2 py-0">
                                        <input class="form-control text-center" type="text" name="mapels[]" required>
                                        <label class="text-field-border"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col" style="min-width: 100px; max-width: 160px">
                                <p class="mb-0 text-sm">Jumlah Pesan</p>
                                <div class="form-group text-field m-0">
                                    <div class="text-field-input px-2 py-0">
                                        <input class="plate_quantity" type="hidden" name="plate_quantities[]" value="1">
                                        <input class="form-control text-center plate_quantity_text" type="text" name="plate_quantity_text[]" value="1" required>
                                        <label class="text-field-border"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto pl-5">
                                <button type="button" class="btn btn-danger btn-sm product-delete" data-product-id="${id_element}" tabIndex="-1">
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
                    if (valueNum < 0) {
                        el.val(0);
                        plateQuantityText.val(0).trigger('change');
                    }
                }).trigger('change');
            });
        });

        $('#product-form').on('click', '.product-delete', function() {
            var productId = $(this).data('product-id');
            $('#product-' + productId).remove();
        });
    });
</script>
@endsection
