@extends('layouts.admin')
@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1 class="m-0">Stock Saldo</h1>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <strong>STOCK SALDO PERIODE TANGGAL</strong>
    </div>
    <div class="card-body">
        <form action="{{ route("admin.stock-saldos.jangka") }}" enctype="multipart/form-data" method="POST">
            @csrf
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label class="required" for="date">Tanggal</label>
                        <x-admin.form-group
                            type="text"
                            id="date"
                            name="date"
                            containerClass=" m-0"
                            boxClass=" px-2 py-1"
                            class="form-control-sm"
                            value="{{ request('date', old('date'))}}"
                            placeholder="Pilih Tanggal"
                        >
                            <x-slot name="right">
                                <button type="button" class="btn btn-sm border-0 btn-default px-2 date-clear" data-action="+" style="display:{{ !request('date', old('date')) ? 'none' : 'block' }}">
                                    <i class="fa fa-times"></i>
                                </button>
                            </x-slot>
                        </x-admin.form-group>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>{{ trans('cruds.bookVariant.fields.type') }}</label>
                        <select class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type">
                            <option value disabled {{ old('type', null) === null ? 'selected' : '' }}>All</option>
                            @foreach(App\Models\BookVariant::TYPE_SELECT as $key => $label)
                                <option value="{{ $key }}" {{ old('type', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('type'))
                            <span class="text-danger">{{ $errors->first('type') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.bookVariant.fields.type_helper') }}</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label for="jenjang_id">{{ trans('cruds.book.fields.jenjang') }}</label>
                        <select class="form-control select2 {{ $errors->has('jenjang') ? 'is-invalid' : '' }}" name="jenjang_id">
                            @foreach($jenjangs as $id => $entry)
                                <option value="{{ $id }}" {{ old('jenjang_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('jenjang'))
                            <span class="text-danger">{{ $errors->first('jenjang') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.book.fields.jenjang_helper') }}</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label for="mapel_id">{{ trans('cruds.book.fields.mapel') }}</label>
                        <select class="form-control select2 {{ $errors->has('mapel') ? 'is-invalid' : '' }}" name="mapel_id">
                            @foreach($mapels as $id => $entry)
                                <option value="{{ $id }}" {{ old('mapel_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('mapel'))
                            <span class="text-danger">{{ $errors->first('mapel') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.book.fields.mapel_helper') }}</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label for="kelas_id">{{ trans('cruds.bookVariant.fields.kelas') }}</label>
                        <select class="form-control select2 {{ $errors->has('kelas') ? 'is-invalid' : '' }}" name="kelas_id">
                            @foreach($kelas as $id => $entry)
                                <option value="{{ $id }}" {{ old('kelas_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('kelas'))
                            <span class="text-danger">{{ $errors->first('kelas') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.bookVariant.fields.kelas_helper') }}</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label for="cover_id">{{ trans('cruds.book.fields.isi') }}</label>
                        <select class="form-control select2 {{ $errors->has('isi') ? 'is-invalid' : '' }}" name="isi_id">
                            @foreach($isis as $id => $entry)
                                <option value="{{ $id }}" {{ old('isi_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('isi'))
                            <span class="text-danger">{{ $errors->first('cover') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.book.fields.cover_helper') }}</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label for="cover_id">{{ trans('cruds.book.fields.cover') }}</label>
                        <select class="form-control select2 {{ $errors->has('cover') ? 'is-invalid' : '' }}" name="cover_id">
                            @foreach($covers as $id => $entry)
                                <option value="{{ $id }}" {{ old('cover_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('cover'))
                            <span class="text-danger">{{ $errors->first('cover') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.book.fields.cover_helper') }}</span>
                    </div>
                </div>
            </div>

            <div class="form-group mt-3">
                <button type="submit" class="btn btn-primary mr-2">Generate</button>
                <button type="submit" value="export" name="export" class="btn btn-warning">Export</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        STOCK SALDO GENERATED PERBULAN
    </div>

    <div class="card-body">
        @can('stock_saldo_create')
            <div class="row mb-3">
                <div class="col-2">
                    <form action="{{ route('admin.stock-saldos.store') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-block">Generate Saldo</button>
                    </form>
                </div>
            </div>
        @endcan
        <form id="filterform">
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label class="required" for="periode">Periode</label>
                        <select class="form-control select2" name="periode" id="periode">
                            @foreach($periode as $id => $entry)
                                <option value="{{ $id }}">{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('periode'))
                            <span class="text-danger">{{ $errors->first('periode') }}</span>
                        @endif
                        <span class="help-block"></span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>{{ trans('cruds.bookVariant.fields.type') }}</label>
                        <select class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type" id="type">
                            <option value disabled {{ old('type', null) === null ? 'selected' : '' }}>All</option>
                            @foreach(App\Models\BookVariant::TYPE_SELECT as $key => $label)
                                <option value="{{ $key }}" {{ old('type', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('type'))
                            <span class="text-danger">{{ $errors->first('type') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.bookVariant.fields.type_helper') }}</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label for="jenjang_id">{{ trans('cruds.book.fields.jenjang') }}</label>
                        <select class="form-control select2 {{ $errors->has('jenjang') ? 'is-invalid' : '' }}" name="jenjang_id" id="jenjang_id">
                            @foreach($jenjangs as $id => $entry)
                                <option value="{{ $id }}" {{ old('jenjang_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('jenjang'))
                            <span class="text-danger">{{ $errors->first('jenjang') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.book.fields.jenjang_helper') }}</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label for="mapel_id">{{ trans('cruds.book.fields.mapel') }}</label>
                        <select class="form-control select2 {{ $errors->has('mapel') ? 'is-invalid' : '' }}" name="mapel_id" id="mapel_id">
                            @foreach($mapels as $id => $entry)
                                <option value="{{ $id }}" {{ old('mapel_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('mapel'))
                            <span class="text-danger">{{ $errors->first('mapel') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.book.fields.mapel_helper') }}</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label for="kelas_id">{{ trans('cruds.bookVariant.fields.kelas') }}</label>
                        <select class="form-control select2 {{ $errors->has('kelas') ? 'is-invalid' : '' }}" name="kelas_id" id="kelas_id">
                            @foreach($kelas as $id => $entry)
                                <option value="{{ $id }}" {{ old('kelas_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('kelas'))
                            <span class="text-danger">{{ $errors->first('kelas') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.bookVariant.fields.kelas_helper') }}</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label for="cover_id">{{ trans('cruds.book.fields.isi') }}</label>
                        <select class="form-control select2 {{ $errors->has('isi') ? 'is-invalid' : '' }}" name="isi_id" id="isi_id">
                            @foreach($isis as $id => $entry)
                                <option value="{{ $id }}" {{ old('isi_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('isi'))
                            <span class="text-danger">{{ $errors->first('cover') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.book.fields.cover_helper') }}</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label for="cover_id">{{ trans('cruds.book.fields.cover') }}</label>
                        <select class="form-control select2 {{ $errors->has('cover') ? 'is-invalid' : '' }}" name="cover_id" id="cover_id">
                            @foreach($covers as $id => $entry)
                                <option value="{{ $id }}" {{ old('cover_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('cover'))
                            <span class="text-danger">{{ $errors->first('cover') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.book.fields.cover_helper') }}</span>
                    </div>
                </div>
            </div>

            <div class="form-group mt-3">
                <button type="submit" class="btn btn-success">Filter</button>
            </div>
        </form>
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-StockSaldo">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.stockSaldo.fields.product') }}
                    </th>
                    <th>
                        {{ trans('cruds.stockSaldo.fields.periode') }}
                    </th>
                    <th>
                        {{ trans('cruds.stockSaldo.fields.qty_awal') }}
                    </th>
                    <th>
                        {{ trans('cruds.stockSaldo.fields.in') }}
                    </th>
                    <th>
                        Adjustment
                    </th>
                    <th>
                        {{ trans('cruds.stockSaldo.fields.out') }}
                    </th>
                    <th>
                        {{ trans('cruds.stockSaldo.fields.qty_akhir') }}
                    </th>
                </tr>
            </thead>
        </table>
    </div>
</div>



@endsection
@section('scripts')
@parent
<script src="https://cdn.jsdelivr.net/npm/@easepick/bundle@1.2.0/dist/index.umd.min.js"></script>
<script>
$(function () {
  let dtOverrideGlobals = {
    processing: true,
    serverSide: true,
    retrieve: true,
    aaSorting: [],
    ajax: {
            url: "{{ route('admin.stock-saldos.index') }}",
            data: function(data) {
                data.periode = $('#periode').val(),
                data.type = $('#type').val(),
                data.isi = $('#isi_id').val(),
                data.cover = $('#cover_id').val(),
                data.jenjang = $('#jenjang_id').val(),
                data.kelas = $('#kelas_id').val(),
                data.mapel = $('#mapel_id').val()
            }
        },
    columns: [
        { data: 'placeholder', name: 'placeholder' },
        { data: 'product_code', name: 'product.code', class: 'text-center' },
        { data: 'periode', name: 'periode', class: 'text-center'  },
        { data: 'qty_awal', name: 'qty_awal', class: 'text-center'  },
        { data: 'in', name: 'in', class: 'text-center'  },
        { data: 'adjustment', name: 'adjustment', class: 'text-center'  },
        { data: 'out', name: 'out', class: 'text-center'  },
        { data: 'qty_akhir', name: 'qty_akhir', class: 'text-center'  }
    ],
    orderCellsTop: true,
    pageLength: 50,
  };
  let table = $('.datatable-StockSaldo').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });

  $("#filterform").submit(function(event) {
        event.preventDefault();
        table.ajax.reload();
    });

    var picker = new easepick.create({
        element: $('#date').get(0),
        css: [
            'https://cdn.jsdelivr.net/npm/@easepick/bundle@1.2.0/dist/index.css',
        ],
        plugins: ['RangePlugin', 'LockPlugin'],
        RangePlugin: {
            tooltip: true,
        },
        LockPlugin: {
            maxDate: new Date(),
        },
    });

    picker.on('select', function(e) {
        $('#date').trigger('change');
        $('.date-clear').show();
    });

    $('.date-clear').on('click', function(e) {
        e.preventDefault();

        picker.clear();
        $(e.currentTarget).hide();
    });

});

</script>
@endsection
