@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h1>Realisasi Pengerjaan Plate</h1>
    </div>

    <div class="card-body">
        @if (session()->has('error-message'))
            <p class="text-danger">
                {{session()->get('error-message')}}
            </p>
        @endif

        <form class="form-prevent-multiple-submits" method="POST" action="{{ route("admin.aquarium.realisasiStore", [$plate_item->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="row">
                <div class="col-12">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>
                                    {{ trans('cruds.platePrint.fields.no_spk') }}
                                </th>
                                <td>
                                    {{ $platePrint->no_spk }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.platePrint.fields.date') }}
                                </th>
                                <td>
                                    {{ $platePrint->date }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.platePrint.fields.semester') }}
                                </th>
                                <td>
                                    {{ $platePrint->semester->name ?? '' }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.platePrint.fields.type') }}
                                </th>
                                <td>
                                    {{ App\Models\PlatePrint::TYPE_SELECT[$platePrint->type] }}
                                </td>
                            </tr>
                            @if (isset($platePrint->vendor))
                            <tr>
                                <th>
                                    {{ trans('cruds.platePrint.fields.vendor') }}
                                </th>
                                <td>
                                    {{ $platePrint->vendor->full_name ?? '' }}
                                </td>
                            </tr>
                            @endif
                            @if (isset($platePrint->customer))
                            <tr>
                                <th>
                                    {{ trans('cruds.platePrint.fields.customer') }}
                                </th>
                                <td>
                                    {{ $platePrint->customer ?? '' }}
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <th>
                                    {{ trans('cruds.platePrint.fields.note') }}
                                </th>
                                <td>
                                    {{ $platePrint->note }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <hr style="margin: 1em -15px;border-color:#ccc" />
            <div id="product-form">
                <div class="item-product" id="product-{{ $plate_item->id }}">
                    <div class="row">
                        <div class="col-3 align-self-center">
                            <h6 class="text-sm product-name mb-1">{{ $plate_item->product ? $plate_item->product->name : $plate_item->product_text }}</h6>
                            <p class="mb-0 text-sm">
                                Plate : <strong>{{ $plate_item->plate ? $plate_item->plate->name : 'Belum Tahu' }}</strong>
                            </p>
                            @if ($plate_item->product)
                                <p class="mb-0 text-sm">
                                    Mapel : <strong>{{ $plate_item->product->mapel->name }}</strong>
                                </p>
                                <p class="mb-0 text-sm">
                                    Kelas : <strong>{{ $plate_item->product->kelas->name }}</strong>
                                </p>
                                <p class="mb-0 text-sm">
                                    Halaman : <strong>{{ $plate_item->product->halaman->code ?? null }}</strong>
                                </p>
                                <p class="mb-0 text-sm">
                                    Kurikulum : <strong>{{ $plate_item->product->kurikulum->name }}</strong>
                                </p>
                                @if ($plate_item->product->cover)
                                    <p class="mb-0 text-sm">
                                        Kolom Nama : <strong>Jangan Lupa !</strong>
                                    </p>
                                    <p class="mb-0 text-sm">
                                        Cover : <strong>{{ $plate_item->product->cover->name  }}</strong>
                                    </p>
                                @else
                                    <p class="mb-0 text-sm">
                                        Naskah : <strong>{{ $plate_item->product->isi->name  }}</strong>
                                    </p>
                                @endif

                            @endif
                        </div>
                        <div class="col offset-1 align-items-end align-self-center">
                            <div class="row mb-3">
                                <div class="col" style="min-width: 100px; max-width: 240px">
                                    <p class="mb-0 text-sm">Quantity Pesan</p>
                                    <div class="form-group text-field m-0">
                                        <div class="text-field-input px-2 py-0">
                                            <input class="estimasi_quantity" type="hidden" name="estimasi_quantity" value="{{ $plate_item->estimasi }}">
                                            <input class="form-control text-center estimasi_quantity_text" type="text" name="estimasi_quantity_text" value="{{ angka($plate_item->estimasi) }}" tabindex="-1" readonly>
                                            <label class="text-field-border"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col" style="min-width: 100px; max-width: 240px">
                                    <p class="mb-0 text-sm">Realisasi</p>
                                    <div class="form-group text-field m-0">
                                        <div class="text-field-input px-2 py-0">
                                            <input class="plate_quantity" type="hidden" name="plate_quantity" value="{{ $plate_item->realisasi }}">
                                            <input class="form-control text-center plate_quantity_text" type="text" name="plate_quantity_text" value="{{ angka($plate_item->realisasi) }}" data-min="{{ $plate_item->realisasi }}" required>
                                            <label class="text-field-border"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col" style="min-width: 100px">
                                    <div class="form-group text-field m-0">
                                        <p class="mb-0 text-sm">Catatan</p>
                                        <textarea style="min-height: 50px !important" class="form-control" name="note">{{ $plate_item->note }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto pl-5 align-items-end align-self-center">
                            <div class="col-auto" style="max-width: 200px">
                                <div class="form-group text-field m-0">
                                    <div class="text-field-input">
                                        <input class="done" type="hidden" name="done" value="{{ $plate_item->status == 'done' ? 1 : 0 }}">
                                        <input class="status bootstrap-switch" type="checkbox" tabindex="-1" value="1" {{ $plate_item->status == 'done' ? 'checked readonly' : '' }} data-on-text="DONE" data-off-text="N/Y">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr style="margin: 1em -15px;border-color:#ccc" />
                </div>
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
        var productForm = $('#product-form');
        var productItem = productForm.find('.item-product');

        productItem.each(function(index, item) {
            var product = $(item);
            var estimasiQuantity = product.find('.estimasi_quantity');
            var estimasiQuantityText = product.find('.estimasi_quantity_text');
            var plateQuantity = product.find('.plate_quantity');
            var plateQuantityText = product.find('.plate_quantity_text');
            var status = product.find('.status');
            var done = product.find('.done');

            status.on('switchChange.bootstrapSwitch', function (event, state) {
                if (state) {
                    done.val(1);
                } else {
                    done.val(0);
                }
            });

            plateQuantityText.on('input', function(e) {
                var value = numeral(e.target.value);

                plateQuantityText.val(value.format('0,0'));
                plateQuantity.val(value.value()).trigger('change');
            }).trigger('change');

            plateQuantity.on('change', function(e) {
                var el = $(e.currentTarget);
                var valueNum = parseInt(el.val());
                var min = parseInt(plateQuantityText.data('min'));
                if (valueNum < min) {
                    el.val(min);
                    plateQuantityText.val(min).trigger('change');
                }
            }).trigger('change');
        });
    });
</script>
@endsection
