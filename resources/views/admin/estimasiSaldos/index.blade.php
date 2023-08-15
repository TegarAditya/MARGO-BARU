@extends('layouts.admin')
@section('content')

<div class="row mb-4">
    <div class="col-12">
        <h1 class="m-0 bold">Saldo Estimasi</h1>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <strong>SALDO ESTIMASI AREA {{ $group_area ? $group_area->name : 'Semua Area' }}</strong>
    </div>
    <div class="card-body">
        <form action="{{ route("admin.estimasi-saldos.index") }}" enctype="multipart/form-data" method="GET">
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label class="required" for="area">{{ trans('cruds.marketingArea.fields.group_area') }}</label>
                        <select class="form-control select2 {{ $errors->has('area') ? 'is-invalid' : '' }}" name="area" id="area">
                            @foreach($group_areas as $id => $entry)
                                <option value="{{ $id }}" {{ old('area', $group_area ? $group_area->id : null) == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover datatable-saldo">
                <thead>
                    <tr>
                        <th></th>
                        <th>Sales</th>
                        <th>Estimasi</th>
                        <th>Dikirim</th>
                        <th>Sisa</th>
                    </tr>
                </thead>
                <tbody>
                @php
                    $total_pesanan = 0;
                    $total_dikirim = 0;
                    $total_sisa = 0;
                @endphp
                @foreach ($saldo as $item)
                    @php
                        $total_pesanan += $item->pesanan;
                        $total_dikirim += $item->dikirim;
                        $total_sisa += $item->sisa;
                    @endphp
                    <tr>
                        <td></td>
                        <td>{{ $item->full_name }}</td>
                        <td class="text-center">{{ angka($item->pesanan) }} Eksemplar</td>
                        <td class="text-center">{{ angka($item->dikirim) }} Eksemplar</td>
                        <td class="text-center">{{ angka($item->sisa) }} Eksemplar</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="text-center">
                            <strong>Total</strong>
                        </td>
                        <td class="text-center">{{ angka($total_pesanan) }} Eksemplar</td>
                        <td class="text-center">{{ angka($total_dikirim) }} Eksemplar</td>
                        <td class="text-center">{{ angka($total_sisa) }} Eksemplar</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@endsection
@section('scripts')
@parent
<script>
    $(function () {
       $('.datatable-saldo').DataTable({
         'paging'      : true,
         'lengthChange': false,
         'searching'   : false,
         'ordering'    : false,
         'info'        : true,
         'autoWidth'   : false,
         'pageLength'  : 50
       })
     })
</script>
@endsection
