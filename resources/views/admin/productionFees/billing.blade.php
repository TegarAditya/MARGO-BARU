@extends('layouts.admin')
@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1 class="m-0 bold">Billing Vendor</h1>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <strong>REKAP BILLING VENDOR PERIODE {{ $start->format('d F Y') }} - {{ $end->format('d F Y') }}</strong>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover datatable-saldo">
                <thead>
                    <tr>
                        <th></th>
                        <th>Vendor</th>
                        <th>Saldo Awal</th>
                        <th>Ongkos</th>
                        <th>Pembayaran</th>
                        <th>Saldo Akhir</th>
                    </tr>
                </thead>
                <tbody>
                @php
                    $total_awal = 0;
                    $total_ongkos = 0;
                    $total_bayar = 0;
                    $total_akhir = 0;
                @endphp
                @foreach ($vendors as $item)
                    @php
                        $awal = $saldo_awal->where('id', $item->id)->first();
                        $pertama = ($awal->cetak + $awal->finishing) - $awal->bayar;

                        $total_awal += $pertama;
                        $total_ongkos += $item->cetak + $item->finishing;
                        $total_bayar += $item->bayar;
                        $terakhir = ($pertama + $item->cetak + $item->finishing) - $item->bayar;
                        $total_akhir += $terakhir;
                    @endphp
                    <tr>
                        <td></td>
                        <td>{{ $item->full_name }}</td>
                        <td class="text-right">{{ money($pertama) }}</td>
                        <td class="text-right">{{ money($item->cetak + $item->finishing) }}</td>
                        <td class="text-right">{{ money($item->bayar) }}</td>
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
                        <td class="text-right">{{ money($total_ongkos) }}</td>
                        <td class="text-right">{{ money($total_bayar) }}</td>
                        <td class="text-right">{{ money($total_akhir) }}</td>
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
