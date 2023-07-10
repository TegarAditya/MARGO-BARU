@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('cruds.rekapBilling.title') }}
    </div>

    <div class="table-responsive mt-5">
        @php
            $totalestimasi = 0;
            $totalpengambilan = 0;
            $totalretur = 0;
            $totalbayar = 0;
            $totaldiskon = 0;
            $totalpiutang = 0;
        @endphp
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
                </tr>
            </thead>
            <tbody>
            @foreach ($saldos as $saldo)
                @php
                    $estimasi = $saldo->order_details->sum('total');
                    $pengambilan = $saldo->invoices->where('nominal', '>', 0)->sum('nominal');
                    $retur = abs($saldo->invoices->where('nominal', '<', 0)->sum('nominal'));
                    $bayar = $saldo->pembayarans->sum('bayar');
                    $diskon = $saldo->pembayarans->sum('diskon');
                    $piutang = $pengambilan - ($retur + $bayar + $diskon);
                    $totalestimasi += $estimasi;
                    $totalpengambilan += $pengambilan;
                    $totalretur += $retur;
                    $totalbayar += $bayar;
                    $totaldiskon += $diskon;
                    $totalpiutang += $piutang;
                @endphp
                <tr>
                    <td></td>
                    <td>{{ $saldo->name }}</td>
                    <td class="text-right">@money($estimasi)</td>
                    <td class="text-right">@money($pengambilan)</td>
                    <td class="text-right">@money($retur)</td>
                    <td class="text-right">@money($bayar)</td>
                    <td class="text-right">@money($diskon)</td>
                    <td class="text-right">@money($piutang)</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="text-center">
                        <strong>Total</strong>
                    </td>
                    <td class="text-right">@money($totalestimasi)</td>
                    <td class="text-right">@money($totalpengambilan)</td>
                    <td class="text-right">@money($totalretur)</td>
                    <td class="text-right">@money($totalbayar)</td>
                    <td class="text-right">@money($totaldiskon)</td>
                    <td class="text-right">@money($totalpiutang)</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>



@endsection