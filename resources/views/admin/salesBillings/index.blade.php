@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('cruds.salesBilling.title') }}
    </div>

    <div class="card-body">
        <div class="table-responsive mt-2">
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
                        <th>Piutang</th>
                        {{-- <th></th> --}}
                    </tr>
                </thead>
                <tbody>
                @php
                    $total_pengambilan = 0;
                    $total_diskon = 0;
                    $total_retur = 0;
                    $total_bayar = 0;
                    $total_potongan = 0;
                    $total_piutang = 0;
                @endphp
                @foreach ($sales as $item)
                    @php
                        $transaksi = $item->transaction_total;

                        $pengambilan = $transaksi ? $transaksi->total_invoice : 0;
                        $diskon = $transaksi ? $transaksi->total_diskon : 0;
                        $retur = $transaksi ? $transaksi->total_retur : 0;
                        $bayar = $transaksi ? $transaksi->total_bayar : 0;
                        $potongan = $transaksi ? $transaksi->total_potongan : 0;

                        $total_pengambilan += $pengambilan;
                        $total_diskon += $diskon;
                        $total_retur += $retur;
                        $total_bayar += $bayar;
                        $total_potongan += $potongan;

                        $piutang = $pengambilan - ($diskon + $retur + $bayar + $potongan);
                        $total_piutang += $piutang;
                    @endphp
                    <tr>
                        <td></td>
                        <td>{{ $item->full_name }}</td>
                        <td class="text-right">{{ money($pengambilan) }}</td>
                        <td class="text-right">{{ money($diskon) }}</td>
                        <td class="text-right">{{ money($retur) }}</td>
                        <td class="text-right">{{ money($bayar) }}</td>
                        <td class="text-right">{{ money($potongan) }}</td>
                        <td class="text-right">{{ money($piutang) }}</td>
                        {{-- <td class="text-center">
                            <a class="px-1" href="{{ route('admin.salespeople.show', $item->id) }}.'" title="Show">
                                <i class="fas fa-eye fa-lg"></i>
                            </a>
                        </td> --}}
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
                        <td class="text-right">{{ money($total_piutang) }}</td>
                        {{-- <td></td> --}}
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