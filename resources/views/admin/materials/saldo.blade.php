@extends('layouts.admin')
@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1 class="m-0 bold">Stock Saldo</h1>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <strong>STOCK SALDO PERIODE {{ $start->format('d F Y') }} - {{ $end->format('d F Y') }}</strong>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover datatable-saldo">
                <thead>
                    <tr>
                        <th></th>
                        <th>Material</th>
                        <th>Saldo Awal</th>
                        <th>In</th>
                        <th>Out</th>
                        <th>Saldo Akhir</th>
                    </tr>
                </thead>
                <tbody>
                @php
                    $total_awal = 0;
                    $total_in = 0;
                    $total_out = 0;
                    $total_akhir = 0;
                @endphp
                @foreach ($materials as $item)
                    @php
                        $awal = $saldo_awal->where('id', $item->id)->first();
                        $pertama = $awal->in - $awal->out;

                        $total_awal += $pertama;
                        $total_in += $item->in;
                        $total_out += $item->out;

                        $terakhir = $pertama + ($item->in - $item->out);
                        $total_akhir += $terakhir;
                    @endphp
                    <tr>
                        <td></td>
                        <td>{{ $item->name }}</td>
                        <td class="text-center">{{ angka($pertama) }}</td>
                        <td class="text-center">{{ angka($item->in) }}</td>
                        <td class="text-center">{{ angka($item->out) }}</td>
                        <td class="text-center">{{ angka($terakhir) }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="text-center">
                            <strong>Total</strong>
                        </td>
                        <td class="text-center">{{ angka($total_awal) }}</td>
                        <td class="text-center">{{ angka($total_in) }}</td>
                        <td class="text-center">{{ angka($total_out) }}</td>
                        <td class="text-center">{{ angka($total_akhir) }}</td>
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
