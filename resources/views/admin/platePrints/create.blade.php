@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h1>Formulir SPK Cetak Plate</h1>
    </div>

    <div class="card-body">
        <form class="form-prevent-multiple-submits" method="POST" action="{{ route("admin.plate-prints.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="no_spk">{{ trans('cruds.platePrint.fields.no_spk') }}</label>
                        <input class="form-control {{ $errors->has('no_spk') ? 'is-invalid' : '' }}" type="text" name="no_spk" id="no_spk" value="{{ old('no_spk', $no_spk) }}" readonly required>
                        @if($errors->has('no_spk'))
                            <span class="text-danger">{{ $errors->first('no_spk') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.platePrint.fields.no_spk_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="date">{{ trans('cruds.platePrint.fields.date') }}</label>
                        <input class="form-control date {{ $errors->has('date') ? 'is-invalid' : '' }}" type="text" name="date" id="date" value="{{ old('date') }}" required>
                        @if($errors->has('date'))
                            <span class="text-danger">{{ $errors->first('date') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.platePrint.fields.date_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="vendor_id">{{ trans('cruds.platePrint.fields.vendor') }}</label>
                        <select class="form-control select2 {{ $errors->has('vendor') ? 'is-invalid' : '' }}" name="vendor_id" id="vendor_id" required>
                            @foreach($vendors as $id => $entry)
                                <option value="{{ $id }}" {{ old('vendor_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('vendor'))
                            <span class="text-danger">{{ $errors->first('vendor') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.platePrint.fields.vendor_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required">{{ trans('cruds.cetak.fields.type') }}</label>
                        <select class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type" id="type" required>
                            <option value disabled {{ old('type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                            @foreach(App\Models\Cetak::TYPE_SELECT as $key => $label)
                                <option value="{{ $key }}" {{ old('type', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('type'))
                            <span class="text-danger">{{ $errors->first('type') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.cetak.fields.type_helper') }}</span>
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
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="isi_cover_id">Isi / Cover</label>
                        <select id="isi_cover_id" class="form-control select2" name="isi_cover_id" style="width: 100%;" required>
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label for="note">{{ trans('cruds.platePrint.fields.note') }}</label>
                        <textarea class="form-control {{ $errors->has('note') ? 'is-invalid' : '' }}" name="note" id="note">{{ old('note') }}</textarea>
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
        $('#product-search').select2({
            templateResult: formatProduct,
            templateSelection: formatProductSelection,
            ajax: {
                    url: "{{ route('admin.book-variants.getCetak') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                            type: $('#type').val(),
                            jenjang: $('#jenjang_id').val(),
                            cover_isi: $('#isi_cover_id').val()
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    },
                    cache: false
                }
        });

        $('#type').on('change', function() {
            $('#isi_cover_id').select2({
                ajax: {
                    url: "{{ route('admin.cetaks.getIsiCover') }}",
                    data: function() {
                        return {
                            type: $('#type').val()
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
                url: "{{ route('admin.book-variants.getInfoCetak') }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    id: productId,
                },
                success: function(product) {
                    var formHtml = `
                        <div class="item-product" id="product-${product.id}">
                            <div class="row">
                                <div class="col-4 align-self-center">
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
                                    <p class="mb-0 text-sm">
                                        <strong>ESTIMASI : ${product.estimasi_produksi.estimasi}</strong>
                                    </p>
                                </div>
                                <div class="col offset-1 row align-items-end align-self-center">
                                    <input type="hidden" name="products[]" value="${product.id}">
                                    <div class="col" style="min-width: 180px">
                                        <p class="mb-0 text-sm">Plate</p>
                                        <div class="form-group text-field m-0">
                                            <select class="form-control text-center plates select2" name="plates[]" style="width: 100%;" tabIndex="-1" required>
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col" style="min-width: 100px">
                                        <p class="mb-0 text-sm">Jumlah Plate</p>
                                        <div class="form-group text-field m-0">
                                            <div class="text-field-input px-2 py-0">
                                                <input class="plate_quantity" type="hidden" name="plate_quantities[]" value="1">
                                                <input class="form-control text-center plate_quantity_text" type="text" name="plate_quantity_text[]" value="1" required>
                                                <label class="text-field-border"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col" style="min-width: 180px">
                                        <p class="mb-0 text-sm">Chemical</p>
                                        <div class="form-group text-field m-0">
                                            <select class="form-control text-center chemical select2" name="chemicals[]" style="width: 100%;" tabIndex="-1" required>
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col" style="min-width: 100px">
                                        <p class="mb-0 text-sm">Jumlah Liter</p>
                                        <div class="form-group text-field m-0">
                                            <div class="text-field-input px-2 py-0">
                                                <input class="form-control text-center chemical_quantity" type="number" name="chemical_quantities[]" value="1" step="0.1" required>
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

                    $('.plates').select2({
                        ajax: {
                            url: "{{ route('admin.materials.getPlateRaws') }}",
                            data: function() {
                                return {
                                    vendor: $('#vendor_id').val()
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

                    $('.chemical').select2({
                        ajax: {
                            url: "{{ route('admin.materials.getChemicals') }}",
                            dataType: 'json',
                            processResults: function(data) {
                                return {
                                    results: data
                                };
                            }
                        }
                    });

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
