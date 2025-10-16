<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class BillingService
{
    public static function getBillSummary(int $semester, bool $includeCurrent = false, $orderDesc = false, ?int $salesperson = null)
    {
        $invoices = DB::table('invoices')
            ->select('salesperson_id', 'semester_id')
            ->whereNull('deleted_at');

        $payments = DB::table('payments')
            ->select('salesperson_id', 'semester_id');

        $returns = DB::table('return_goods')
            ->select('salesperson_id', 'semester_retur_id as semester_id');

        if ($salesperson !== null) {
            $invoices->where('salesperson_id', $salesperson);
            $payments->where('salesperson_id', $salesperson);
            $returns->where('salesperson_id', $salesperson);
        }

        $allActivities = $invoices->union($payments)->union($returns);

        $invoiceSummary = DB::table('invoices')
            ->select(
                'salesperson_id',
                'semester_id',
                DB::raw('SUM(total) as jual'),
                DB::raw('SUM(discount) as diskon')
            )
            ->whereNull('deleted_at')
            ->groupBy('salesperson_id', 'semester_id');

        $returnSummary = DB::table('return_goods')
            ->select(
                'salesperson_id',
                'semester_retur_id',
                DB::raw('SUM(nominal) as total_return_goods_nominal')
            )
            ->groupBy('salesperson_id', 'semester_retur_id');

        $paymentSummary = DB::table('payments')
            ->select(
                'salesperson_id',
                'semester_id',
                DB::raw('SUM(amount) as total_payments'),
                DB::raw('SUM(paid) as total_paid'),
                DB::raw('SUM(discount) as total_discount')
            )
            ->groupBy('salesperson_id', 'semester_id');

        $query = DB::query()
            ->fromSub($allActivities, 'base')
            ->leftJoinSub($invoiceSummary, 'i', function ($join) {
                $join->on('base.salesperson_id', '=', 'i.salesperson_id')
                    ->on('base.semester_id', '=', 'i.semester_id');
            })
            ->leftJoinSub($returnSummary, 'r', function ($join) {
                $join->on('base.salesperson_id', '=', 'r.salesperson_id')
                    ->on('base.semester_id', '=', 'r.semester_retur_id');
            })
            ->leftJoinSub($paymentSummary, 'p', function ($join) {
                $join->on('base.salesperson_id', '=', 'p.salesperson_id')
                    ->on('base.semester_id', '=', 'p.semester_id');
            })
            ->leftJoin('semesters as s', 'base.semester_id', '=', 's.id')
            ->select(
                'base.salesperson_id',
                'base.semester_id',
                's.name AS semester_name',
                DB::raw('COALESCE(i.jual, 0) AS jual'),
                DB::raw('COALESCE(i.diskon, 0) AS diskon'),
                DB::raw('COALESCE(i.jual, 0) - COALESCE(i.diskon, 0) AS jual_diskon'),
                DB::raw('COALESCE(r.total_return_goods_nominal, 0) AS retur'),
                DB::raw('COALESCE(i.jual, 0) - COALESCE(i.diskon, 0) - COALESCE(r.total_return_goods_nominal, 0) AS tagihan'),
                DB::raw('COALESCE(p.total_paid, 0) AS bayar'),
                DB::raw('COALESCE(p.total_discount, 0) AS potongan'),
                DB::raw('COALESCE(p.total_payments, 0) AS pembayaran'),
                DB::raw('COALESCE(i.jual, 0) - COALESCE(i.diskon, 0) - COALESCE(r.total_return_goods_nominal, 0) - COALESCE(p.total_payments, 0) AS saldo_akhir')
            );

        $query->where('base.semester_id', $includeCurrent ? '<=' : '<', $semester);

        if ($salesperson !== null) {
            $query->where('base.salesperson_id', $salesperson);
        }

        return $query->havingRaw('saldo_akhir != 0')
            ->orderBy('base.semester_id', $orderDesc ? 'desc' : 'asc')
            ->get();
    }
}
