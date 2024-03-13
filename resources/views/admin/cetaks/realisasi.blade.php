@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h1>Formulir Realisasi Cetak Cover & Isi</h1>
    </div>

    <div class="card-body">
        <form class="form-prevent-multiple-submits" method="POST" action="{{ route("admin.cetaks.realiasasiStore", [$cetak->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="no_spc">{{ trans('cruds.cetak.fields.no_spc') }}</label>
                        <input class="form-control {{ $errors->has('no_spc') ? 'is-invalid' : '' }}" type="text" name="no_spc" id="no_spc" value="{{ old('no_spc', $cetak->no_spc) }}" readonly>
                        @if($errors->has('no_spc'))
                            <span class="text-danger">{{ $errors->first('no_spc') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.cetak.fields.no_spc_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="date">{{ trans('cruds.cetak.fields.date') }}</label>
                        <input class="form-control date {{ $errors->has('date') ? 'is-invalid' : '' }}" type="text" name="date" id="date" value="{{ old('date', $cetak->date) }}" required>
                        @if($errors->has('date'))
                            <span class="text-danger">{{ $errors->first('date') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.cetak.fields.date_helper') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="required">{{ trans('cruds.salesOrder.fields.semester') }}</label>
                        <select class="form-control select2 {{ $errors->has('semester') ? 'is-invalid' : '' }}" name="semester_id" id="semester_id" disabled>
                            @foreach($semesters as $id => $entry)
                                <option value="{{ $id }}" {{ (old('semester_id') ? old('semester_id') : $cetak->semester_id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
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
                        <select class="form-control select2 {{ $errors->has('vendor') ? 'is-invalid' : '' }}" name="vendor_id" id="vendor_id" disabled>
                            @foreach($vendors as $id => $entry)
                                <option value="{{ $id }}" {{ (old('vendor_id') ? old('vendor_id') : $cetak->vendor->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
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
                        <select class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type" id="type" disabled>
                            <option value disabled {{ old('type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                            @foreach(App\Models\Cetak::TYPE_SELECT as $key => $label)
                                <option value="{{ $key }}" {{ old('type', $cetak->type) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
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
                        <label for="jenjang_id">{{ trans('cruds.bookVariant.fields.jenjang') }}</label>
                        <select class="form-control select2 {{ $errors->has('jenjang') ? 'is-invalid' : '' }}" name="jenjang_id" id="jenjang_id" disabled>
                            @foreach($jenjangs as $id => $entry)
                                <option value="{{ $id }}" {{ (old('jenjang_id') ? old('jenjang_id') : $cetak->jenjang_id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('jenjang'))
                            <span class="text-danger">{{ $errors->first('jenjang') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.bookVariant.fields.jenjang_helper') }}</span>
                    </div>
                </div>
                {{-- <div class="col-12">
                    <div class="form-group">
                        <label for="note">{{ trans('cruds.cetak.fields.note') }}</label>
                        <textarea class="form-control {{ $errors->has('note') ? 'is-invalid' : '' }}" name="note" id="note">{{ old('note', $cetak->note) }}</textarea>
                        @if($errors->has('note'))
                            <span class="text-danger">{{ $errors->first('note') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.cetak.fields.note_helper') }}</span>
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
                @foreach ($cetak_items as $item)
                    @php
                        $product = $item->product;
                    @endphp
                    <div class="item-product" id="product-{{ $product->id }}">
                        <div class="row">
                            <div class="col-6 align-self-center">
                                <h6 class="text-sm product-name mb-1">({{ $product->book_type }}) {{ $product->short_name }}</h6>
                                <p class="mb-0 text-sm">
                                    Code : <strong>{{ $product->code }}</strong>
                                </p>
                                <p class="mb-0 text-sm">
                                    Jenjang - Kurikulum  : <strong>{{ $product->jenjang->name }} - {{ $product->kurikulum->name }}</strong>
                                </p>
                                <p class="mb-0 text-sm">
                                    <strong>STOCK : {{ $product->stock }}</strong>
                                </p>
                                <p class="mb-0 text-sm">
                                    <strong>ESTIMASI : {{ $product->estimasi_produksi ? $product->estimasi_produksi->estimasi : 0 }}</strong>
                                </p>
                                <p class="mb-0 text-sm">
                                    <strong>ESTIMASI BARU : {{ $product->estimasi_produksi ? $product->estimasi_produksi->estimasi_baru : 0 }}</strong>
                                </p>
                            </div>
                            <div class="col offset-1 row align-items-end align-self-center">
                                <input type="hidden" name="products[]" value="{{ $product->id }}">
                                <input type="hidden" name="cetak_items[]" value="{{ $item->id }}">
                                <div class="col" style="max-width: 160px">
                                    <p class="mb-0 text-sm">SPC Cetak</p>
                                    <div class="form-group text-field m-0">
                                        <div class="text-field-input px-2 py-0">
                                            <input class="estimasi" type="hidden" name="estimasis[]"  value="{{ $item->estimasi }}">
                                            <input class="form-control text-center estimasi_text" type="text" name="estimasi_text[]" value="{{ angka($item->estimasi) }}" readonly tabindex="-1">
                                            <label class="text-field-border"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col" style="max-width: 160px">
                                    <p class="mb-0 text-sm">Realisasi</p>
                                    <div class="form-group text-field m-0">
                                        <div class="text-field-input px-2 py-0">
                                            <input class="quantity" type="hidden" name="quantities[]" value="{{ $item->quantity }}">
                                            <input class="form-control text-center quantity_text" type="text" name="quantity_text[]" value="{{ angka($item->quantity ) }}" {{ $item->done ? 'tabindex="-1" ' : 'required' }}>
                                            <label class="text-field-border"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-auto" style="max-width: 200px">
                                    <div class="form-group text-field m-0">
                                        <div class="text-field-input">
                                            <input class="done" type="hidden" name="done[]" value="{{ $item->done }}">
                                            <input class="status bootstrap-switch" type="checkbox" {{ $item->done ? 'checked readonly' : '' }} tabindex="-1" value="1" data-on-text="DONE" data-off-text="N/Y">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr style="margin: 1em -15px;border-color:#ccc" />
                    </div>
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
    $(function() {
        var productForm = $('#product-form');
        var productItem = productForm.find('.item-product');

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
    });

});
</script>
@endsection
