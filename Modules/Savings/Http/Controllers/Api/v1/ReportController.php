<?php

namespace Modules\Savings\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware(['permission:savings.savings.reports.transactions'])->only(['transaction']);
        $this->middleware(['permission:savings.savings.reports.balances'])->only(['balance']);
        $this->middleware(['permission:savings.savings.reports.accounts'])->only(['account']);

    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function transaction(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $branch_id = $request->branch_id;
        $data = [];
        if (!empty($start_date)) {
            $data = DB::table("savings_transactions")
                ->join("savings", "savings_transactions.savings_id", "savings.id")
                ->join("savings_products", "savings.savings_product_id", "savings_products.id")
                ->join("branches", "savings.branch_id", "branches.id")
                ->join("clients", "savings.client_id", "clients.id")
                ->leftJoin("payment_details", "savings_transactions.payment_detail_id", "payment_details.id")
                ->leftJoin("users", "savings.savings_officer_id", "users.id")
                ->leftJoin("payment_types", "payment_details.payment_type_id", "payment_types.id")
                ->when($start_date, function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('savings_transactions.submitted_on', [$start_date, $end_date]);
                })->when($branch_id, function ($query) use ($branch_id) {
                    $query->where('savings_transactions.branch_id', $branch_id);
                })->where('savings_transactions.reversed', 0)
                ->selectRaw("concat(clients.first_name,' ',clients.last_name) client,concat(users.first_name,' ',users.last_name) savings_officer,branches.name branch,payment_details.receipt,payment_types.name payment_type,savings_products.name savings_product,savings_transactions.id,savings_transactions.name transaction_type,savings_transactions.amount,savings_transactions.credit,savings_transactions.debit,savings_transactions.balance,savings_transactions.submitted_on,savings.id savings_id")
                ->get();

        }
        return response()->json(['data' => $data]);
    }

    public function account(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $branch_id = $request->branch_id;
        $savings_officer_id = $request->savings_officer_id;
        $savings_product_id = $request->savings_product_id;
        $data = [];

        if (!empty($start_date)) {
            $data = DB::table("savings")
                ->leftJoin("savings_transactions", "savings_transactions.savings_id", "savings.id")
                ->join("savings_products", "savings.savings_product_id", "savings_products.id")
                ->join("branches", "savings.branch_id", "branches.id")
                ->join("clients", "savings.client_id", "clients.id")
                ->leftJoin("users", "savings.savings_officer_id", "users.id")
                ->when($start_date, function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('savings.submitted_on_date', [$start_date, $end_date]);
                })->when($branch_id, function ($query) use ($branch_id) {
                    $query->where('savings.branch_id', $branch_id);
                })
                ->when($savings_officer_id, function ($query) use ($savings_officer_id) {
                    $query->where('savings.savings_officer_id', $savings_officer_id);
                })
                ->selectRaw("concat(clients.first_name,' ',clients.last_name) client,concat(users.first_name,' ',users.last_name) savings_officer,branches.name branch,savings_products.name savings_product,clients.gender,sum(savings_transactions.credit) credit,sum(savings_transactions.debit) debit,savings.balance_derived balance,savings.submitted_on_date,savings.id id")
                ->get();
        }
        return response()->json(['data' => $data]);
    }

    public function balance(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $branch_id = $request->branch_id;
        $savings_officer_id = $request->savings_officer_id;
        $savings_product_id = $request->savings_product_id;
        $data = [];

        if (!empty($start_date)) {
            $data = DB::table("savings_transactions")
                ->join("savings", "savings_transactions.savings_id", "savings.id")
                ->join("savings_products", "savings.savings_product_id", "savings_products.id")
                ->join("branches", "savings.branch_id", "branches.id")
                ->leftJoin("users", "savings.savings_officer_id", "users.id")
                ->when($start_date, function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('savings_transactions.submitted_on', [$start_date, $end_date]);
                })->when($branch_id, function ($query) use ($branch_id) {
                    $query->where('savings.branch_id', $branch_id);
                })
                ->when($savings_officer_id, function ($query) use ($savings_officer_id) {
                    $query->where('savings.savings_officer_id', $savings_officer_id);
                })
                ->when($savings_product_id, function ($query) use ($savings_product_id) {
                    $query->where('savings.savings_product_id', $savings_product_id);
                })
                ->selectRaw("concat(users.first_name,' ',users.last_name) savings_officer,branches.name branch,savings_products.name savings_product, credit, debit,savings_transactions.submitted_on")
                ->get();

        }
        return response()->json(['data' => $data]);
    }
}
