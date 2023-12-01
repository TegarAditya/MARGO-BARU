@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.productionEstimation.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.production-estimations.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th width="30%">
                            {{ trans('cruds.productionEstimation.fields.product') }}
                        </th>
                        <td>
                            {{ $productionEstimation->product->code ?? '' }}<br>
                            {{ $productionEstimation->product->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productionEstimation.fields.estimasi') }}
                        </th>
                        <td>
                            {{ $productionEstimation->estimasi }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Estimasi Dari Sales
                        </th>
                        <td>
                            {{ $productionEstimation->sales }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Estimasi Dari Eksternal
                        </th>
                        <td>
                            {{ $productionEstimation->eksternal }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Estimasi Dari Internal
                        </th>
                        <td>
                            {{ $productionEstimation->internal }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Yang telah Di Produksi (SPK)
                        </th>
                        <td>
                            {{ $productionEstimation->produksi }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Yang telah Di Produksi (Realisasi)
                        </th>
                        <td>
                            {{ $productionEstimation->realisasi }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <h3 class="mt-5 mb-3">History Product Movement</h3>
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-movement">
                    <thead>
                        <tr>
                            <th>

                            </th>
                            <th>
                                Movement
                            </th>
                            <th>
                                Reference
                            </th>
                            <th>
                                Quantity
                            </th>
                            <th>
                                Date
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($estimationMovement as $key => $movement)
                            <tr data-entry-id="{{ $movement->id }}">
                                <td></td>
                                <td class="text-center">
                                    {{ App\Models\EstimationMovement::TYPE_SELECT[$movement->type] ?? '' }}
                                </td>
                                <td class="text-center">
                                    @if ($movement->reference_id)
                                        @if ($movement->reference_type == 'sales_order')
                                            <span class="mr-2"><a href="{{ route('admin.estimations.show', $movement->reference_id) }}"><i class="fas fa-eye text-success fa-lg"></i></a></span> {{ $movement->reference->no_estimasi ?? '-' }} <br> {{ $movement->reference ? $movement->reference->salesperson->short_name : '-' }}
                                        @elseif ($movement->reference_type == 'cetak')
                                            <span class="mr-2"><a href="{{ route('admin.cetaks.show', $movement->reference_id) }}"><i class="fas fa-eye text-success fa-lg"></i></a></span> {{ $movement->reference->no_spc ?? '-'}}
                                        @elseif ($movement->transaction_type == 'finishing')
                                            <span class="mr-2"><a href="{{ route('admin.finishings.show', $movement->reference_id) }}"><i class="fas fa-eye text-success fa-lg"></i></a></span> {{ $movement->reference->no_spk ?? '-'}}
                                        @endif
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{ angka($movement->quantity) }}
                                </td>
                                <td class="text-center">
                                    {{ $movement->created_at ?? '' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



@endsection
@section('scripts')
@parent
<script>
    $(function () {
       $('.datatable-movement').DataTable({
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
