@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h1>Formulir Edit Pengiriman</h1>
    </div>

    <div class="card-body">

        @if (session()->has('error-message'))
            <p class="text-danger">
                {{session()->get('error-message')}}
            </p>
        @endif

        <form class="form-prevent-multiple-submits" method="POST" action="{{ route("admin.delivery-orders.adjustSave") }}" enctype="multipart/form-data" onkeypress="return event.keyCode != 13;">
            @csrf
            <input type="hidden" name="delivery_id" id="delivery_id" value="{{$deliveryOrder->id}}">
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="no_suratjalan">{{ trans('cruds.deliveryOrder.fields.no_suratjalan') }}</label>
                        <input class="form-control {{ $errors->has('no_suratjalan') ? 'is-invalid' : '' }}" type="text" name="no_suratjalan" id="no_suratjalan" value="{{ old('no_suratjalan', noRevisi($deliveryOrder->no_suratjalan)) }}" readonly>
                        @if($errors->has('no_suratjalan'))
                            <span class="text-danger">{{ $errors->first('no_suratjalan') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.deliveryOrder.fields.no_suratjalan_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="date">{{ trans('cruds.deliveryOrder.fields.date') }}</label>
                        <input class="form-control date {{ $errors->has('date') ? 'is-invalid' : '' }}" type="text" name="date" id="date" value="{{ old('date', $deliveryOrder->date) }}" required>
                        @if($errors->has('date'))
                            <span class="text-danger">{{ $errors->first('date') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.deliveryOrder.fields.date_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="salesperson_id">{{ trans('cruds.deliveryOrder.fields.salesperson') }}</label>
                        <select class="form-control select2 {{ $errors->has('salesperson') ? 'is-invalid' : '' }}" name="salesperson_id" id="salesperson_id" disabled>
                            @foreach($salespeople as $id => $entry)
                                <option value="{{ $id }}" {{ (old('salesperson_id') ? old('salesperson_id') : $deliveryOrder->salesperson->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('salesperson'))
                            <span class="text-danger">{{ $errors->first('salesperson') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.deliveryOrder.fields.salesperson_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="jenjang_id">{{ trans('cruds.salesOrder.fields.jenjang') }}</label>
                        <select class="form-control select2 {{ $errors->has('jenjang') ? 'is-invalid' : '' }}" name="jenjang_id" id="jenjang_id">
                            @foreach($jenjangs as $id => $entry)
                                <option value="{{ $id }}" {{ old('jenjang_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('jenjang'))
                            <span class="text-danger">{{ $errors->first('jenjang') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.salesOrder.fields.jenjang_helper') }}</span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-2">
                    <div class="form-group">
                        <button class="btn btn-success btn-block" data-toggle="modal" data-target="#listModal" type="button">
                            List
                        </button>
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

<div class="modal fade" id="listModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Daftar Surat Jalan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class='col-md-12'>
                        @php
                            $total_estimasi = 0;
                        @endphp
                        <table cellspacing="0" cellpadding="0" class="table table-sm table-bordered" style="width: 100%">
                            <thead>
                                <tr>
                                    <th class="text-center" width="1%">No.</th>
                                    <th>Jenjang</th>
                                    <th>Tema/Mapel</th>
                                    <th class="text-center px-3" width="1%">Quantity</th>
                                </tr>
                            </thead>

                            <tbody>
                                @php
                                    $total_item = 0;
                                @endphp
                                @foreach($delivery_items as $item)
                                    @php
                                    $product = $item->product;
                                    $total_item += $item->quantity;
                                    @endphp
                                    <tr>
                                        <td class="text-right px-3">{{ $loop->iteration }}.</td>
                                        <td class="text-center">{{ $product->jenjang->name ?? '' }} - {{ $product->kurikulum->code ?? '' }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td class="text-center px-3">{{ $item->quantity }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="text-center" colspan="3"><b>Total</b></th>
                                    <th class="text-center">{{ angka($total_item) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
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
                    url: "{{ route('admin.delivery-orders.getEstimasi') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                            salesperson: $('#salesperson_id').val(),
                            jenjang: $('#jenjang_id').val(),
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
                url: "{{ route('admin.delivery-orders.getInfoEstimasi') }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    id: productId,
                    salesperson: $('#salesperson_id').val()
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
                                        Jenjang - Kurikulum : <strong>${product.jenjang.name} - ${product.kurikulum.name}</strong>
                                    </p>
                                    <p class="mb-0 text-sm">
                                        <strong>ESTIMASI : ${product.estimasi}</strong>
                                    </p>
                                    <p class="mb-0 text-sm">
                                        <strong>TERKIRIM : ${product.terkirim}</strong>
                                    </p>
                                    <p class="mb-0 text-sm">
                                        <strong>STOCK : ${product.stock}</strong>
                                    </p>
                                </div>
                                <div class="col offset-1 row align-items-end align-self-center">
                                    <div class="col" style="max-width: 200px">
                                        <p class="mb-0 text-sm">Dikirim</p>
                                        <div class="form-group text-field m-0">
                                            <div class="text-field-input px-2 py-0">
                                                <input type="hidden" name="products[]" value="${product.id}">
                                                <input type="hidden" name="orders[]" value="${product.order_id}">
                                                <input class="quantity" type="hidden" name="quantities[]" data-max="${Math.min(product.estimasi - product.terkirim, product.stock)}" value="1">
                                                <input class="form-control text-center quantity_text" type="text" name="quantity_text[]" value="1" required>
                                                <label class="text-field-border"></label>
                                            </div>
                                        </div>
                                    </div>
                    `;
                    if (product.type == 'L') {
                        formHtml += `<div class="col" style="min-width: 240px">
                                        <p class="mb-0 text-sm">Pegangan Guru</p>
                                        <div class="form-group text-field m-0">
                                            <select class="form-control text-center pegeh" name="pgs[]" style="width: 100%;" tabIndex="-1" data-product="${product.id}" data-order="${product.order_id}">
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col" style="max-width: 160px">
                                        <p class="mb-0 text-sm">Dikirim PG</p>
                                        <div class="form-group text-field m-0">
                                            <div class="text-field-input px-2 py-0">
                                                <input class="pg_quantity" type="hidden" name="pg_quantities[]" value="0">
                                                <input class="form-control text-center pg_quantity_text" type="text" name="pg_quantity_text[]" value="0" required>
                                                <label class="text-field-border"></label>
                                            </div>
                                        </div>
                                    </div>`;
                    }
                    formHtml += `<div class="col-auto pl-5">
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

                    sortItems();

                    $('.pegeh').select2({
                        ajax: {
                            url: "{{ route('admin.book-variants.getPgDelivery') }}",
                            data: function() {
                                return {
                                    product: $(this).data('product'),
                                    order: $(this).data('order'),
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
                                        title: 'Pegangan Guru Not Found',
                                        text: 'Pegangan Guru Not Foundk',
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
                        var pgQuantity = product.find('.pg_quantity');
                        var pgQuantityText = product.find('.pg_quantity_text');
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
                                        max = valueNum + 100;
                                        quantity.data('max', valueNum + 100);
                                    }
                                });
                            }
                        }).trigger('change');

                        pgQuantityText.on('input', function(e) {
                            var value = numeral(e.target.value);

                            pgQuantityText.val(value.format('0,0'));
                            pgQuantity.val(value.value()).trigger('change');
                        }).trigger('change');

                        pgQuantity.on('change', function(e) {
                            var el = $(e.currentTarget);
                            var valueNum = parseInt(el.val());
                            if (valueNum < 0) {
                                el.val(0);
                                pgQuantityText.val(0).trigger('change');
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

    function sortItems() {
        var productForm = $('#product-form');
        var items = productForm.find('.item-product').get();

        items.sort(function(a, b) {
            const idA = parseInt(a.id.split('-')[1]);
            const idB = parseInt(b.id.split('-')[1]);
            return idA - idB;
        });

        $("select.pegeh.select2-hidden-accessible").select2('destroy');

        productForm.empty().append(items);
    }
</script>
@endsection
