<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;

class BillingService
{
    public static function getBillSummary(int $salesperson, int $semester, bool $includeCurrent = false)
    {
        return DB::table('invoices as i')
            ->leftJoin(
                DB::raw('(
                SELECT 
                    salesperson_id, 
                    semester_retur_id, 
                    SUM(nominal) AS total_return_goods_nominal 
                FROM 
                    return_goods 
                GROUP BY 
                    salesperson_id, semester_retur_id
            ) AS r'),
                function ($join) {
                    $join->on('i.salesperson_id', '=', 'r.salesperson_id')
                        ->on('i.semester_id', '=', 'r.semester_retur_id');
                }
            )
            ->leftJoin(
                DB::raw('(
                SELECT 
                    salesperson_id, 
                    semester_id, 
                    SUM(amount) AS total_payments 
                FROM 
                    payments 
                GROUP BY 
                    salesperson_id, semester_id
            ) AS p'),
                function ($join) {
                    $join->on('i.salesperson_id', '=', 'p.salesperson_id')
                        ->on('i.semester_id', '=', 'p.semester_id');
                }
            )
            ->leftJoin('semesters as s', 'i.semester_id', '=', 's.id')
            ->select(
                'i.salesperson_id',
                'i.semester_id',
                's.name AS semester_name',
                DB::raw('COALESCE(SUM(i.total), 0) AS jual'),
                DB::raw('COALESCE(SUM(i.discount), 0) AS diskon'),
                DB::raw('COALESCE(SUM(i.total), 0) - COALESCE(SUM(i.discount), 0) AS jual_diskon'),
                DB::raw('COALESCE(r.total_return_goods_nominal, 0) AS retur'),
                DB::raw('COALESCE(SUM(i.total), 0) - COALESCE(SUM(i.discount), 0) - COALESCE(r.total_return_goods_nominal, 0) AS tagihan'),
                DB::raw('COALESCE(p.total_payments, 0) AS pembayaran'),
                DB::raw('COALESCE(SUM(i.total), 0) - COALESCE(SUM(i.discount), 0) - COALESCE(r.total_return_goods_nominal, 0) - COALESCE(p.total_payments, 0) AS saldo_akhir')
            )
            ->where('i.salesperson_id', $salesperson)
            ->where('i.semester_id', $includeCurrent ? '<=' : '<', $semester)
            ->whereNull('i.deleted_at')
            ->groupBy('i.salesperson_id', 'i.semester_id', 's.name', 'r.total_return_goods_nominal', 'p.total_payments')
            ->havingRaw('saldo_akhir != 0')
            ->get();
    }
}
