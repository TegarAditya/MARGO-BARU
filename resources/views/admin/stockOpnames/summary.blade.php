@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('cruds.stockOpname.title') }} Summary
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-3">
                <div class="info-box">
                    <span class="info-box-icon bg-primary"><i class="fas fa-book"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Jumlah Item Buku</span>
                        <span class="info-box-number">{{ angka($buku->count())}}</span>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="info-box">
                    <span class="info-box-icon bg-primary"><i class="fas fa-book"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Jumlah Item Pegangan Guru</span>
                        <span class="info-box-number">{{ angka($pg->count())}}</span>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-box"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Stock Buku</span>
                        <span class="info-box-number">{{ angka($buku->sum('stock')) }} Eksemplar</span>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-box"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Stock Pegangan Guru</span>
                        <span class="info-box-number">{{ angka($pg->sum('stock')) }} Eksemplar</span>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-dollar-sign"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Harga Buku</span>
                        <span class="info-box-number">{{ money($buku->sum('total_price')) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
$(function () {

});
</script>
@endsection
