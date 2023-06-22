@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.salesOrder.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.sales-orders.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="semester_id">{{ trans('cruds.salesOrder.fields.semester') }}</label>
                <select class="form-control select2 {{ $errors->has('semester') ? 'is-invalid' : '' }}" name="semester_id" id="semester_id" required>
                    @foreach($semesters as $id => $entry)
                        <option value="{{ $id }}" {{ old('semester_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('semester'))
                    <span class="text-danger">{{ $errors->first('semester') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.salesOrder.fields.semester_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="salesperson_id">{{ trans('cruds.salesOrder.fields.salesperson') }}</label>
                <select class="form-control select2 {{ $errors->has('salesperson') ? 'is-invalid' : '' }}" name="salesperson_id" id="salesperson_id" required>
                    @foreach($salespeople as $id => $entry)
                        <option value="{{ $id }}" {{ old('salesperson_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('salesperson'))
                    <span class="text-danger">{{ $errors->first('salesperson') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.salesOrder.fields.salesperson_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="product_id">{{ trans('cruds.salesOrder.fields.product') }}</label>
                <select class="form-control select2 {{ $errors->has('product') ? 'is-invalid' : '' }}" name="product_id" id="product_id" required>
                    @foreach($products as $id => $entry)
                        <option value="{{ $id }}" {{ old('product_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('product'))
                    <span class="text-danger">{{ $errors->first('product') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.salesOrder.fields.product_helper') }}</span>
            </div>
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
            <div class="form-group">
                <label for="kurikulum_id">{{ trans('cruds.salesOrder.fields.kurikulum') }}</label>
                <select class="form-control select2 {{ $errors->has('kurikulum') ? 'is-invalid' : '' }}" name="kurikulum_id" id="kurikulum_id">
                    @foreach($kurikulums as $id => $entry)
                        <option value="{{ $id }}" {{ old('kurikulum_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('kurikulum'))
                    <span class="text-danger">{{ $errors->first('kurikulum') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.salesOrder.fields.kurikulum_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="quantity">{{ trans('cruds.salesOrder.fields.quantity') }}</label>
                <input class="form-control {{ $errors->has('quantity') ? 'is-invalid' : '' }}" type="number" name="quantity" id="quantity" value="{{ old('quantity', '') }}" step="1" required>
                @if($errors->has('quantity'))
                    <span class="text-danger">{{ $errors->first('quantity') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.salesOrder.fields.quantity_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="moved">{{ trans('cruds.salesOrder.fields.moved') }}</label>
                <input class="form-control {{ $errors->has('moved') ? 'is-invalid' : '' }}" type="number" name="moved" id="moved" value="{{ old('moved', '0') }}" step="1">
                @if($errors->has('moved'))
                    <span class="text-danger">{{ $errors->first('moved') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.salesOrder.fields.moved_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="retur">{{ trans('cruds.salesOrder.fields.retur') }}</label>
                <input class="form-control {{ $errors->has('retur') ? 'is-invalid' : '' }}" type="number" name="retur" id="retur" value="{{ old('retur', '0') }}" step="1">
                @if($errors->has('retur'))
                    <span class="text-danger">{{ $errors->first('retur') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.salesOrder.fields.retur_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>

    <div class="card-body">
        <form method="post" action="{{ route('admin.sales-orders.store') }}">
            @csrf

            <div class="form-group">
                <label for="customer_name">Customer Name</label>
                <input type="text" class="form-control" id="customer_name" name="customer_name" required>
            </div>
            <div class="form-group">
                <label for="order_number">Order Number</label>
                <input type="text" class="form-control" id="order_number" name="order_number" required>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <textarea class="form-control" id="address" name="address" required></textarea>
            </div>
            <div class="form-group">
                <label for="product">Product</label>
                <select class="form-control" id="product" name="product[]" multiple required></select>
                <div id="product_details" class="mt-3">
                    <label>Product Details</label>
                    <div class="form-row">
                        <div class="col-md-4">
                            <label>Quantity</label>
                        </div>
                        <div class="col-md-4">
                            <label>Price</label>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('#product').select2({
            ajax: {
                url: "{{ route('admin.book-variants.getBooks') }}",
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.code,
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 1
        });

        // Listen for changes to product selection
        $('#product').on('change', function() {
            // Clear existing product details
            $('#product_details').empty();

            // Add quantity and price fields for each selected product
            $.each($(this).val(), function(index, productId) {
                var $productDetails = $('<div>', { class: 'form-row mt-2' });

                var $productName = $('<label>', { text: 'Product Name', class: 'col-md-2' });
                var $quantityLabel = $('<label>', { text: 'Quantity', class: 'col-md-2' });
                var $priceLabel = $('<label>', { text: 'Price', class: 'col-md-2' });

                var $productNameInput = $('<input>', { type: 'text', class: 'form-control col-md-4', value: '', readonly: true });
                var $quantityInput = $('<input>', { type: 'number', class: 'form-control col-md-2 ml-2', placeholder: 'Quantity', name: 'quantity[]' });
                var $priceInput = $('<input>', { type: 'number', class: 'form-control col-md-2 ml-2', placeholder: 'Price', name: 'price[]' });

                $productDetails.append($productName);
                $productDetails.append($productNameInput);
                $productDetails.append($quantityLabel);
                $productDetails.append($quantityInput);
                $productDetails.append($priceLabel);
                $productDetails.append($priceInput);

                // Fetch the product name via AJAX and set it in the product name input
                $.ajax({
                    url: "{{ route('admin.book-variants.getBook') }}" +  '?id=' + productId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $productNameInput.val(response.name);
                        $priceInput.val(response.price)
                    }
                });

                $('#product_details').append($productDetails);
            });
        });
    });
</script>
@endsection

<div class="row">
    <div class="col-8 align-self-center">
        <h6 class="text-sm product-name mb-1">PENDIDIKAN AGAMA ISLAM DAN BUDI PEKERTI - KELAS 7 -
            HAL 64 - SEMESTER GENAP 2022/2023</h6>

        <p class="mb-0 text-sm">
            Cover - Isi : <span class="product-category">MMJ - MERDEKA</span>
        </p>

        <p class="mb-0 text-sm">
            Jenjang: <span class="product-category">SMP MERDEKA</span>
        </p>

        <p class="mb-0 text-sm">
            Cover - Isi : <span class="product-category">MMJ - MERDEKA</span>
        </p>

        <p class="mb-0 text-sm">
            Jenjang: <span class="product-category">SMP MERDEKA</span>
        </p>
    </div>

    <div class="col row align-items-end align-self-center">
        <div class="col" style="max-width: 240px">
            <p class="mb-0 text-sm">Harga</p>
            <div class="form-group text-field m-0">
                <div class="text-field-input px-2 py-0">
                    <input class="form-control text-center" type="number" value="0" min="1">
                    <label class="text-field-border"></label>
                </div>
            </div>
        </div>
        <div class="col" style="max-width: 240px">
            <p class="mb-0 text-sm">Harga</p>
            <div class="form-group text-field m-0">
                <div class="text-field-input px-2 py-0">
                    <span class="text-sm mr-1">Rp</span>
                    <input class="form-control" type="number" value="0" min="1">
                    <label class="text-field-border"></label>
                </div>
            </div>
        </div>
    </div>
</div>
<hr style="margin: 1em -15px;border-color:#ccc" />