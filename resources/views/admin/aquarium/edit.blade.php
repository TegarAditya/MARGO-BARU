@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h1>Checklist Pengerjaan Plate</h1>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.aquarium.update", [$platePrint->id]) }}" enctype="multipart/form-data">
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
            @if($plate_items->count() > 0)
                <hr style="margin: 1em -15px;border-color:#ccc" />
                <div id="product-form">
                    @foreach ($plate_items as $item)
                        <div class="item-product" id="product-{{ $item->id }}">
                            <div class="row">
                                <div class="col-3 align-self-center">
                                    <h6 class="text-sm product-name mb-1">{{ $item->product ? $item->product->name : $item->product_text }}</h6>
                                    <p class="mb-0 text-sm">
                                        Plate : <strong>{{ $item->plate ? $item->plate->name : 'Belum Tahu' }}</strong>
                                    </p>
                                    @if ($item->product)
                                        <p class="mb-0 text-sm">
                                            Mapel : <strong>{{ $item->product->mapel->name }}</strong>
                                        </p>
                                        <p class="mb-0 text-sm">
                                            Kelas : <strong>{{ $item->product->kelas->name }}</strong>
                                        </p>
                                        <p class="mb-0 text-sm">
                                            Kurikulum : <strong>{{ $item->product->kurikulum->name }}</strong>
                                        </p>
                                        @if ($item->product->cover)
                                            <p class="mb-0 text-sm">
                                                Kolom Nama : <strong>Jangan Lupa !</strong>
                                            </p>
                                            <p class="mb-0 text-sm">
                                                Cover : <strong>{{ $item->product->cover->name  }}</strong>
                                            </p>
                                        @else
                                            <p class="mb-0 text-sm">
                                                Naskah : <strong>{{ $item->product->isi->name  }}</strong>
                                            </p>
                                        @endif

                                    @endif
                                </div>
                                <div class="col offset-1 align-items-end align-self-center">
                                    <div class="row mb-3">
                                        <input type="hidden" name="plate_items[]" value="{{ $item->id }}">
                                        <div class="col" style="min-width: 240px">
                                            <p class="mb-0 text-sm">Plate</p>
                                            <div class="form-group text-field m-0">
                                                <select class="form-control text-center plates select2" name="plates[]" style="width: 100%;" tabIndex="-1" required>
                                                    <option value="">Belum Tahu</option>
                                                    @foreach($materials as $id => $entry)
                                                        <option value="{{ $id }}" {{ $item->plate_id == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                                    @endforeach
                                                </select>
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
                                    <div class="row mb-3">
                                        <div class="col" style="min-width: 100px">
                                            <div class="form-group text-field m-0">
                                            <p class="mb-0 text-sm">Mapel Ready ?</p>
                                                <div class="form-check">
                                                    <input class="check-ready" type="hidden" name="check_mapel[]" value="{{ $item->check_mapel }}">
                                                    <input class="form-check-input check_mapel" type="checkbox" value="1" {{ $item->check_mapel == 1 ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="check_mapel"><b>{{ $item->product ? $item->product->mapel->name : 'Check Mapel' }}</b></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col" style="min-width: 100px">
                                            <div class="form-group text-field m-0">
                                            <p class="mb-0 text-sm">Kelas Ready ?</p>
                                                <div class="form-check">
                                                    <input class="check-ready" type="hidden" name="check_kelas[]" value="{{ $item->check_kelas }}">
                                                    <input class="form-check-input check_kelas" type="checkbox" value="1" {{ $item->check_kelas == 1 ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="check_kelas"><b>{{ $item->product ? $item->product->kelas->name : 'Check Kelas' }}</b></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col" style="min-width: 100px">
                                            <div class="form-group text-field m-0">
                                            <p class="mb-0 text-sm">Kurikulum Ready ?</p>
                                                <div class="form-check">
                                                    <input class="check-ready" type="hidden" name="check_kurikulum[]" value="{{ $item->check_kurikulum }}">
                                                    <input class="form-check-input check_kurikulum" type="checkbox" value="1" {{ $item->check_kurikulum == 1 ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="check_kurikulum"><b>{{ $item->product ? $item->product->kurikulum->name : 'Check Kurikulum' }}</b></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if ($item->product)
                                    <div class="row">
                                        @if ($item->product->cover)
                                            <div class="col" style="min-width: 100px">
                                                <div class="form-group text-field m-0">
                                                <p class="mb-0 text-sm">Kolom Nama Ready ?</p>
                                                    <div class="form-check">
                                                        <input class="check-ready" type="hidden" name="check_kolomnama[]" value="{{ $item->check_kolomnama }}">
                                                        <input class="form-check-input check_kolomnama" type="checkbox" value="1" {{ $item->check_kolomnama == 1 ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="check_kolomnama"><b>Check Kolom Nama</b></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col" style="min-width: 100px">
                                                <div class="form-group text-field m-0">
                                                <p class="mb-0 text-sm">Cover Ready ?</p>
                                                    <div class="form-check">
                                                        <input class="check-ready" type="hidden" name="check_naskah[]" value="{{ $item->check_naskah }}">
                                                        <input class="form-check-input check_naskah" type="checkbox" value="1" {{ $item->check_naskah == 1 ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="check_naskah"><b>{{ $item->product ? $item->product->cover->name : 'Check Cover' }}</b></label>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                        <div class="col" style="min-width: 100px">
                                            <div class="form-group text-field m-0">
                                            <p class="mb-0 text-sm">Naskah Ready ?</p>
                                                <div class="form-check">
                                                    <input class="check-ready" type="hidden" name="check_naskah[]" value="{{ $item->check_naskah }}">
                                                    <input class="form-check-input check_naskah" type="checkbox" value="1" {{ $item->check_naskah == 1 ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="check_naskah"><b>{{ $item->product ? $item->product->isi->name : 'Check Naskah' }}</b></label>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                    </div>
                                    @endif
                                </div>
                                <input type="hidden" name="check_ready[]" value="0">
                                <div class="col-auto pl-5 align-items-end align-self-center">
                                    <button type="button" class="btn btn-success btn-sm btn-ready" tabIndex="-1" style="display: {{ $item->status == 'created' ? 'none' : 'block' }}">
                                        <i class="fa fa-check"> <strong>READY</strong></i>
                                    </button>
                                </div>
                            </div>
                            <hr style="margin: 1em -15px;border-color:#ccc" />
                        </div>
                    @endforeach
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <button class="btn btn-danger" type="submit">
                                Submit
                            </button>
                        </div>
                    </div>
                </div>
            @else
                <h1>SPK Sudah Dikerjakan Semua</h1>
                <div class="row mt-3">

                    <div class="col">
                        <a class="btn btn-primary" href="{{ url()->previous() }}">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                    </div>

                    <div class="col-auto">

                    </div>
                </div>
            @endif
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
            var plateQuantity = product.find('.plate_quantity');
            var plateQuantityText = product.find('.plate_quantity_text');
            var checkMapel = product.find('.check_mapel');
            var mapelValue = product.find('[name="check_mapel[]"]');
            var checkKelas = product.find('.check_kelas');
            var kelasValue = product.find('[name="check_kelas[]"]');
            var checkKurikulum = product.find('.check_kurikulum');
            var kurikulumValue = product.find('[name="check_kurikulum[]"]');
            var checkKolomNama = product.find('.check_kolomnama');
            var kolomnamaValue = product.find('[name="check_kolomnama[]"]');
            var checkNaskah = product.find('.check_naskah');
            var naskahValue = product.find('[name="check_naskah[]"]');
            var readyValue = product.find('[name="check_ready[]"]');

            checkMapel.on('change', function() {
                if ($(this).is(':checked')) {
                    mapelValue.val(1);
                } else {
                    mapelValue.val(0);
                }
                checkInputs();
            });

            checkKelas.on('change', function() {
                if ($(this).is(':checked')) {
                    kelasValue.val(1);
                } else {
                    kelasValue.val(0);
                }
                checkInputs();
            });

            checkKurikulum.on('change', function() {
                if ($(this).is(':checked')) {
                    kurikulumValue.val(1);
                } else {
                    kurikulumValue.val(0);
                }
                checkInputs();
            });

            checkKolomNama.on('change', function() {
                if ($(this).is(':checked')) {
                    kolomnamaValue.val(1);
                } else {
                    kolomnamaValue.val(0);
                }
                checkInputs();
            });

            checkNaskah.on('change', function() {
                if ($(this).is(':checked')) {
                    naskahValue.val(1);
                } else {
                    naskahValue.val(0);
                }
                checkInputs();
            });

            function checkInputs() {
                let allReady = true;
                product.find('.check-ready').each(function() {
                    if ($(this).val() !== '1') {
                        allReady = false;
                        return false;
                    }
                });

                if (allReady) {
                    readyValue.val(1);
                    product.find('.btn-ready').show();
                } else {
                    product.find('.btn-ready').hide();
                }
            }

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
    });
</script>
@endsection
