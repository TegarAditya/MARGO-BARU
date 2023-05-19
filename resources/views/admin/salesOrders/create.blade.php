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
        <form action="{{ route('admin.sales-orders.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="customer">Customer</label>
                <select id="customer" name="customer" class="form-control">
                    <option value="">Select a customer</option>
                    @foreach ($salespeople as $id => $entry)
                        <option value="{{ $id }}">{{ $entry }}</option>
                    @endforeach
                </select>
            </div>

            <div id="products-container">
                <div class="form-group">
                    <label>Products</label>
                    <div class="product-row">
                        <select name="products[0][id]" class="form-control product-select" required>
                            <option value="">Select a product</option>
                            @foreach ($products as $id => $entry)
                                <option value="{{ $id }}">{{ $entry }}</option>
                            @endforeach
                        </select>
                        <input type="number" name="products[0][quantity]" class="form-control" placeholder="Quantity" required>
                        <input type="number" name="products[0][price]" class="form-control" placeholder="Price" required>
                        <button type="button" class="btn btn-danger btn-sm remove-product">Remove</button>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-primary btn-sm" id="add-product">Add Product</button>
            <button type="submit" class="btn btn-success">Submit</button>
        </form>
    </div>
</div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Initialize Select2 for customer selection
            $('#customer').select2();

            // Initialize Select2 for product selection
            $('.product-select').select2({
                ajax: {
                    url: '/products/search',
                    dataType: 'json',
                    delay: 250,
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1
            });

            // Add product row
            $('#add-product').on('click', function () {
                var index = $('.product-row').length;
                var productRow = $('.product-row').first().clone();
                productRow.find('.product-select').attr('name', 'products[' + index + '][id]');
                productRow.find('input[name^="products"]').attr('name', 'products[' + index + '][quantity]');
                productRow.find('input[name^="products"]').attr('name', 'products[' + index + '][price]');
                productRow.find('.remove-product').show();
                $('#products-container').append(productRow);
                productRow.find('.product-select').select2({
                    ajax: {
                        url: '/products/search',
                        dataType: 'json',
                        delay: 250,
                        processResults: function (data) {
                            return {
                                results: data
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 1
                });
            });

            // Remove product row
            $(document).on('click', '.remove-product', function () {
                $(this).closest('.product-row').remove();
            });
        });
    </script>
@endsection
