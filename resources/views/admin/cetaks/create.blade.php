@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h1>Formulir SPK Cetak</h1>
    </div>

    <div class="card-body">
        @if (session()->has('error-message'))
            <p class="text-danger">
                {{session()->get('error-message')}}
            </p>
        @endif

        <form class="form-prevent-multiple-submits" method="POST" action="{{ route("admin.cetaks.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="no_spc">{{ trans('cruds.cetak.fields.no_spc') }}</label>
                        <input class="form-control {{ $errors->has('no_spc') ? 'is-invalid' : '' }}" type="text" name="no_spc" id="no_spc" value="{{ old('no_spc', $no_spc) }}" readonly>
                        @if($errors->has('no_spc'))
                            <span class="text-danger">{{ $errors->first('no_spc') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.cetak.fields.no_spc_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="date">{{ trans('cruds.cetak.fields.date') }}</label>
                        <input class="form-control date {{ $errors->has('date') ? 'is-invalid' : '' }}" type="text" name="date" id="date" value="{{ old('date', $today) }}" required>
                        @if($errors->has('date'))
                            <span class="text-danger">{{ $errors->first('date') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.cetak.fields.date_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required">{{ trans('cruds.salesOrder.fields.semester') }}</label>
                        <select class="form-control select2 {{ $errors->has('semester') ? 'is-invalid' : '' }}" name="semester_id" id="semester_id" required>
                            @foreach($semesters as $id => $entry)
                                <option value="{{ $id }}" {{ (old('semester_id') ? old('semester_id') : setting('current_semester') ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('semester'))
                            <span class="text-danger">{{ $errors->first('semester') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.salesOrder.fields.semester_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="vendor_id">{{ trans('cruds.cetak.fields.vendor') }}</label>
                        <select class="form-control select2 {{ $errors->has('vendor') ? 'is-invalid' : '' }}" name="vendor_id" id="vendor_id" required>
                            @foreach($vendors as $id => $entry)
                                <option value="{{ $id }}" {{ old('vendor_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('vendor'))
                            <span class="text-danger">{{ $errors->first('vendor') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.cetak.fields.vendor_helper') }}</span>
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
                        <label class="required" for="isi_cover_id">Isi / Cover</label>
                        <select id="isi_cover_id" class="form-control select2" name="isi_cover_id" style="width: 100%;" required>
                        </select>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="jenjang_id">{{ trans('cruds.bookVariant.fields.jenjang') }}</label>
                        <select class="form-control select2 {{ $errors->has('jenjang') ? 'is-invalid' : '' }}" name="jenjang_id" id="jenjang_id">
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
                        <label for="kurikulum_id">{{ trans('cruds.book.fields.kurikulum') }}</label>
                        <select class="form-control select2 {{ $errors->has('kurikulum') ? 'is-invalid' : '' }}" name="kurikulum_id" id="kurikulum_id">
                            @foreach($kurikulums as $id => $entry)
                                <option value="{{ $id }}" {{ old('kurikulum_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('kurikulum'))
                            <span class="text-danger">{{ $errors->first('kurikulum') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.book.fields.kurikulum_helper') }}</span>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label for="note">{{ trans('cruds.cetak.fields.note') }}</label>
                        <textarea class="form-control {{ $errors->has('note') ? 'is-invalid' : '' }}" name="note" id="note">{{ old('note') }}</textarea>
                        @if($errors->has('note'))
                            <span class="text-danger">{{ $errors->first('note') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.cetak.fields.note_helper') }}</span>
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
        $('#status').on('switchChange.bootstrapSwitch', function (event, state) {
            if (state) {
                $('#pakeestimasi').val(1);
            } else {
                $('#pakeestimasi').val(0);
            }
        });

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
                            kurikulum: $('#kurikulum_id').val(),
                            semester: $('#semester_id').val(),
                            cover_isi: $('#isi_cover_id').val(),
                            estimasi: $('#pakeestimasi').val()
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
                                <div class="col-5 align-self-center">
                                    <h6 class="text-sm product-name mb-1">(${product.book_type}) ${product.short_name}</h6>
                                    <p class="mb-0 text-sm">
                                        Code : <strong>${product.code}</strong>
                                    </p>
                                    <p class="mb-0 text-sm">
                                        Jenjang - Kurikulum : <strong>${product.jenjang?.name} - ${product.kurikulum?.name}</strong>
                                    </p>
                                    <p class="mb-0 text-sm">
                                        Halaman : <strong>${product.halaman?.name}</strong>
                                    </p>
                                    <p class="mb-0 text-sm">
                                        <strong>STOCK : ${product.stock}</strong>
                                    </p>
                                    <p class="mb-0 text-sm">
                                        <strong>ESTIMASI : ${product.estimasi_produksi ? product.estimasi_produksi.estimasi : 0}</strong>
                                    </p>
                                    <p class="mb-0 text-sm">
                                        <strong>ESTIMASI BARU: ${product.estimasi_produksi ? product.estimasi_produksi.estimasi_baru : 0}</strong>
                                    </p>
                                </div>
                                <div class="col offset-1 row align-items-end align-self-center">
                                    <input type="hidden" name="products[]" value="${product.id}">
                                    <div class="col" style="min-width: 240px">
                                        <p class="mb-0 text-sm">Plate</p>
                                        <div class="form-group text-field m-0">
                                            <select class="form-control text-center plates select2" name="plates[]" style="width: 100%;" tabIndex="-1" data-product="${product.id}" required>
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col" style="max-width: 160px">
                                        <p class="mb-0 text-sm">Jumlah Plate</p>
                                        <div class="form-group text-field m-0">
                                            <div class="text-field-input px-2 py-0">
                                                <input class="plate_quantity" type="hidden" name="plate_quantities[]" value="1">
                                                <input class="form-control text-center plate_quantity_text" type="text" name="plate_quantity_text[]" value="1" required>
                                                <label class="text-field-border"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col" style="max-width: 160px">
                                        <p class="mb-0 text-sm">Cetak</p>
                                        <div class="form-group text-field m-0">
                                            <div class="text-field-input px-2 py-0">
                                                <input class="quantity" type="hidden" name="quantities[]" data-max="${product.estimasi_produksi ? product.estimasi_produksi.estimasi_baru : 0}" value="1">
                                                <input class="form-control text-center quantity_text" type="text" name="quantity_text[]" value="1" required>
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
                    $('#product-form').append(formHtml);
                    $('#product-search').val(null).trigger('change');

                    var productForm = $('#product-form');
                    var productItem = productForm.find('.item-product');

                    $('.plates').select2({
                        ajax: {
                            url: "{{ route('admin.materials.getPlateRaws') }}",
                            data: function() {
                                return {
                                    vendor: $('#vendor_id').val(),
                                    product: $(this).data('product')
                                };
                            },
                            dataType: 'json',
                            processResults: function(data) {
                                if (data.length > 0) {
                                    // If data is not empty, return the processed results
                                    return {
                                        results: data
                                    };
                                } else {
                                    // If data is empty, show the SweetAlert alert and return empty results
                                    Swal.fire({
                                        title: 'Plate Not Found',
                                        text: 'Plate Belum Dicetak',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonText: 'Okay',
                                        cancelButtonText: 'Cancel'
                                    });

                                    return {
                                        results: []
                                    };
                                }
                            }
                        }
                    });

                    productItem.each(function(index, item) {
                        var product = $(item);
                        var quantity = product.find('.quantity');
                        var quantityText = product.find('.quantity_text');
                        var plateQuantity = product.find('.plate_quantity');
                        var plateQuantityText = product.find('.plate_quantity_text');
                        var max = quantity.data('max');

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

                            if (valueNum > max) {
                                Swal.fire({
                                    title: 'Quantity Exceeded',
                                    text: 'The input quantity exceeds the maximum allowed.',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonText: 'I Know',
                                    cancelButtonText: 'Cancel'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        max = valueNum;
                                        quantity.data('max', valueNum);
                                    }
                                });
                            }
                        }).trigger('change');

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

        function showEmptyDataAlert() {
            swal({
                title: 'No data found!',
                text: 'The list is empty.',
                icon: 'warning'
            });
        }
    });
</script>
@endsection
