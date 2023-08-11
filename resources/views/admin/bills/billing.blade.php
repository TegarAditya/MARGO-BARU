@extends('layouts.admin')
@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1 class="m-0 bold">Billing</h1>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <strong>REKAP PIUTANG PERIODE {{ $start->format('d F Y') }} - {{ $end->format('d F Y') }}</strong>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover datatable-saldo">
                <thead>
                    <tr>
                        <th></th>
                        <th>Sales</th>
                        <th>Saldo Awal</th>
                        <th>Faktur</th>
                        <th>Diskon</th>
                        <th>Retur</th>
                        <th>Pembayaran</th>
                        <th>Potongan</th>
                        <th>Saldo Akhir</th>
                    </tr>
                </thead>
                <tbody>
                @php
                    $total_awal = 0;
                    $total_pengambilan = 0;
                    $total_diskon = 0;
                    $total_retur = 0;
                    $total_bayar = 0;
                    $total_potongan = 0;
                    $total_piutang = 0;
                @endphp
                @foreach ($sales as $item)
                    @php
                        $awal = $saldo_awal->where('id', $item->id)->first();
                        $pertama = $awal->pengambilan - ($awal->diskon + $awal->retur + $awal->bayar + $awal->potongan);

                        $total_awal += $pertama;
                        $total_pengambilan += $item->pengambilan;
                        $total_diskon += $item->diskon;
                        $total_retur += $item->retur;
                        $total_bayar += $item->bayar;
                        $total_potongan += $item->potongan;

                        $terakhir = $pertama + ($item->pengambilan - ($item->diskon + $item->retur + $item->bayar + $item->potongan));
                        $total_piutang += $terakhir;
                    @endphp
                    <tr>
                        <td></td>
                        <td>{{ $item->full_name }}</td>
                        <td class="text-right">{{ money($pertama) }}</td>
                        <td class="text-right">{{ money($item->pengambilan) }}</td>
                        <td class="text-right">{{ money($item->diskon) }}</td>
                        <td class="text-right">{{ money($item->retur) }}</td>
                        <td class="text-right">{{ money($item->bayar) }}</td>
                        <td class="text-right">{{ money($item->potongan) }}</td>
                        <td class="text-right">{{ money($terakhir) }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="text-center">
                            <strong>Total</strong>
                        </td>
                        <td class="text-right">{{ money($total_awal) }}</td>
                        <td class="text-right">{{ money($total_pengambilan) }}</td>
                        <td class="text-right">{{ money($total_diskon) }}</td>
                        <td class="text-right">{{ money($total_retur) }}</td>
                        <td class="text-right">{{ money($total_bayar) }}</td>
                        <td class="text-right">{{ money($total_potongan) }}</td>
                        <td class="text-right">{{ money($total_piutang) }}</td>
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
