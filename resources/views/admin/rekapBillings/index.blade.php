@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <strong>BILLING {{ $semester->name }}</strong>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route("admin.rekap-billings.index") }}">
            <div class="row">
                <div class="col row">
                    <div class="col-4">
                        <div class="form-group mb-0">
                            <label class="small mb-0" for="semester">Semester</label>
                            <select class="form-control select2 {{ $errors->has('semester') ? 'is-invalid' : '' }}" name="semester" id="semester">
                                @foreach($semesters as $id => $entry)
                                    <option value="{{ $id }}" {{ (old('semester') ? old('semester') : request()->get('semester') ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                <div class="col-auto align-self-end">
                    <button type="submit" class="btn btn-success">Filter</button>
                </div>
            </div>
        </form>

        <div class="table-responsive mt-5">
            <table class="table table-bordered table-striped table-hover datatable-saldo">
                <thead>
                    <tr>
                        <th></th>
                        <th>Sales</th>
                        <th>Faktur</th>
                        <th>Diskon</th>
                        <th>Retur</th>
                        <th>Pembayaran</th>
                        <th>Potongan</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @php
                    $total_pengambilan = 0;
                    $total_diskon = 0;
                    $total_retur = 0;
                    $total_bayar = 0;
                    $total_potongan = 0;
                @endphp
                @foreach ($sales as $item)
                    @php
                        $total_pengambilan += $item->pengambilan;
                        $total_diskon += $item->diskon;
                        $total_retur += $item->retur;
                        $total_bayar += $item->bayar;
                        $total_potongan += $item->potongan;
                    @endphp
                    <tr>
                        <td></td>
                        <td>{{ $item->full_name }}</td>
                        <td class="text-right">{{ money($item->pengambilan) }}</td>
                        <td class="text-right">{{ money($item->diskon) }}</td>
                        <td class="text-right">{{ money($item->retur) }}</td>
                        <td class="text-right">{{ money($item->bayar) }}</td>
                        <td class="text-right">{{ money($item->potongan) }}</td>
                        <td class="text-center">
                            <a class="px-1" href="{{ route('admin.rekap-billings.billing', ['salesperson' => $item->id, 'semester' => $semester->id]) }}" title="Show" target="_blank">
                                <i class="fas fa-eye fa-lg"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="text-center">
                            <strong>Total</strong>
                        </td>
                        <td class="text-right">{{ money($total_pengambilan) }}</td>
                        <td class="text-right">{{ money($total_diskon) }}</td>
                        <td class="text-right">{{ money($total_retur) }}</td>
                        <td class="text-right">{{ money($total_bayar) }}</td>
                        <td class="text-right">{{ money($total_potongan) }}</td>
                        <td></td>
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
