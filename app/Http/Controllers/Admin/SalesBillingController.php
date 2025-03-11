<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Salesperson;
use Illuminate\Support\Facades\DB;

class SalesBillingController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('sales_billing_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $sales = Salesperson::with('transaction_total')->get();

        return view('admin.salesBillings.index', compact('sales'));
    }

    public function show()
    {
        abort_if(Gate::denies('sales_billing_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.salesBillings.show', compact('salesBilling'));
    }
}
